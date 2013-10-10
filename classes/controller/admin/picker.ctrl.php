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

class Controller_Admin_Picker extends \Nos\Controller_Admin_Appdesk
{
    public function before()
    {
        $this->bypass = true;
        parent::before();
    }

    public function prepare_i18n()
    {
        parent::prepare_i18n();
        \Nos\I18n::current_dictionary('novius_metadata::common');
    }

    public function load_config()
    {
        $metadata_class_name = \Input::get('metadata_class_name', null);
        $model = \Input::get('model', null);

        if (empty($metadata_class_name) || empty($model)) {
            throw new \Exception('You must specify a model and a metadata class name.');
        }

        $metadata_class = \Arr::get($model::getMetadataClasses(), $metadata_class_name, null);
        if (empty($metadata_class)) {
            throw new \Exception('The metadata class name "'.$metadata_class_name
                .'" not found for model "'.$model.'".');
        }

        $nature = \Arr::get($metadata_class, 'nature');
        $nature_model = is_array($nature) ? \Arr::get($nature, 'model', null) : $nature;

        $this->config['model'] = $nature_model;
        $params = array();
        if (is_array($nature) && $query = \Arr::get($nature, 'query', false)) {
            $params = $query;
        }
        if (!isset($params['order_by'])) {
            $params['order_by'] = $nature_model::title_property();
        }
        if (is_array($params['where'])) {
            $where = function ($query) use ($params) {
                $query->where($params['where']);

                return $query;
            };
            $params['callback'] = array($where);
        }
        $this->config['query'] = $params;

        parent::load_config();

        if (count($this->config['dataset']) <= 2) {
            $this->config['appdesk']['appdesk']['grid']['columns'] = array_merge(
                array(
                    array(
                        'headerText' => __('Title'),
                        'dataKey' => '_title',
                    ),
                ),
                $this->config['appdesk']['appdesk']['grid']['columns']
            );
        }

        $this->config['search_text'] = $nature_model::title_property();

        $this->config['appdesk']['appdesk']['buttons'] = array();
        $this->config['appdesk']['appdesk']['grid']['columns']['actions']['actions'] = array(
            'pick' => array(
                'label' => __('Pick'),
                'icon' => 'check',
                'text' => true,
                'primary' => true,
                'action' => array(
                    'action' => 'dialogPick',
                    'event' => 'select_nature',
                ),
            ),
        );

        $context = \Input::get('context', null);
        if (!empty($context)) {
            $this->config['appdesk']['appdesk']['selectedContexts'] = array(\Input::get('context', null));
            $this->config['hideContexts'] = true;
        }
        $this->config['appdesk']['appdesk']['values']['model'] = $model;
        $this->config['appdesk']['appdesk']['values']['metadata_class_name'] = $metadata_class_name;
        $this->config['appdesk']['appdesk']['grid']['urlJson'] .= '?model='.urlencode($model).
            '&metadata_class_name='.urlencode($metadata_class_name);
        if (isset($this->config['appdesk']['appdesk']['treeGrid'])) {
            $this->config['appdesk']['appdesk']['treeGrid']['urlJson'] .= '?model='.urlencode($model).
                '&metadata_class_name='.urlencode($metadata_class_name);
        }

        return $this->config;
    }
}
