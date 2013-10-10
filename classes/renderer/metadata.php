<?php
/**
 * Metadata is an application for Novius OS for adding metadata on models.
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_metadata
 */

namespace Novius\Metadata;

class Renderer_Metadata extends \Nos\Renderer
{
    protected static $DEFAULT_RENDERER_OPTIONS = array(
        'single' => true,
        'select_limit' => 50,
        'metadata_class' => array(),
        'i18n' => array(),
    );

    public static function _init()
    {
        \Nos\I18n::current_dictionary('novius_metadata::common');
    }

    public function set_value($value, $repopulate = false)
    {
        if (empty($value)) {
            $this->value = null;
            return $this;
        }
        if ($this->isSingle()) {
            if (is_array($value)) {
                $value = array_shift($value);
                $value = $value->id;
            }
            $this->value = $value;
        } else {
            $first = current($value);
            if (is_object($first)) {
                $value = array_keys($value);
            }
            $this->value = $value;
        }
        return $this;
    }

    public function before_save($item, $data)
    {
        $natures = (array) $data[$this->name];
        foreach ($item->metadata as $metadata) {
            if ($metadata->metadata_class !== $this->getMetadataClassName()) {
                continue;
            }
            if (in_array($metadata->metadata_nature_id, $natures)) {
                $natures = array_diff($natures, array($metadata->metadata_nature_id));
            } else {
                unset($item->metadata[$metadata->metadata_id]);
                $metadata->delete();
            }
        }

        $class = get_class($item);
        $metadata_class = $this->getMetadataClass();
        $model_nature = \Arr::get($metadata_class, 'nature');
        foreach ($natures as $nature_id) {
            $item->metadata[] = Model_Metadata::forge(array(
                'metadata_item_table' => $class::table(),
                'metadata_item_id' => $item->id,
                'metadata_class' => $this->getMetadataClassName(),
                'metadata_nature_table' => $model_nature::table(),
                'metadata_nature_id' => $nature_id,
            ));
        }
        return false;
    }

    /**
     * Build the field
     *
     * @return  string
     */
    public function build()
    {
        $metadata_class = $this->getMetadataClass();

        $nature = \Arr::get($metadata_class, 'nature');
        $nature_model = is_array($nature) ? \Arr::get($nature, 'model', null) : $nature;
        if ($this->label === $this->name) {
            $this->label = strtr(__('{{metadata_class}}:'), array(
                '{{metadata_class}}' => \Arr::get($metadata_class, 'label'),
            ));
        }

        $count = $nature_model::query()->count();
        $behaviour_tree = $nature_model::behaviours('Nos\Orm_Behaviour_Tree');
        if ($count > \Arr::get($this->renderer_options, 'select_limit', 50) || !empty($behaviour_tree)) {
            return $this->buildRenderer($metadata_class);
        } else {
            return $this->buildSelect($metadata_class, $nature, $nature_model);
        }
    }

    protected function buildRenderer($metadata_class)
    {
        parent::build();

        $this->fieldset()->append(\View::forge('novius_metadata::renderer/javascript', array(
            'field' => $this,
            'single' => $this->isSingle(),
            'metadata_class_name' => $this->getMetadataClassName(),
            'metadata_class' => $metadata_class,
            'i18n' => \Arr::get($this->renderer_options, 'i18n', array()),
        ), false));

        return $this->template((string) \View::forge('novius_metadata::renderer/inputs', array(
            'field' => $this,
            'single' => $this->isSingle(),
            'metadata_class' => $metadata_class,
        ), false));
    }

    protected function buildSelect($metadata_class, $nature, $nature_model)
    {
        $this->type  = 'select';
        $this->options = array();
        if ($this->isSingle()) {
            $this->options[''] = \Arr::get($this->renderer_options, 'i18n.choose',
                strtr(__('Choose a "{{metadata_class}}":'), array(
                        '{{metadata_class}}' => \Arr::get($metadata_class, 'label'),
                    )
                ));
        } else {
            $this->set_attribute('multiple', true);
        }
        $params = array();
        if (is_array($nature) && $query = \Arr::get($nature, 'query', false)) {
            $params = $query;
        }
        if (!isset($params['order_by'])) {
            $params['order_by'] = $nature_model::title_property();
        }
        $natures = $nature_model::find('all', $params);
        foreach ($natures as $nature) {
            $this->options[$nature->id] = $nature->title_item();
        }

        return (string) parent::build();
    }

    protected function getMetadataClassName()
    {
        return str_replace('metadata_', '', $this->name);
    }

    protected function getMetadataClass()
    {
        return \Arr::get($this->renderer_options, 'metadata_class', array());
    }

    protected function isSingle()
    {
        return \Arr::get($this->renderer_options, 'single', false);
    }
}
