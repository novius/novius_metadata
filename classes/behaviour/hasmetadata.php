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

class Behaviour_Hasmetadata extends \Nos\Orm_Behaviour
{
    protected $_properties = array(
        'exclude' => array(),
        'classes' => array(),
    );

    public static function _init()
    {
        \Nos\I18n::current_dictionary('novius_metadata::common');
        \Config::load('novius_metadata::metadata_classes', true);
    }

    public function __construct($class)
    {
        parent::__construct($class);
        if (!isset($this->_properties['exclude'])) {
            $this->_properties['exclude'] = array();
        }
        if (!isset($this->_properties['classes'])) {
            $this->_properties['classes'] = array();
        }
    }

    /**
     * Add relations for linked media and wysiwyg shared with other context
     */
    public function buildRelations()
    {
        $class = $this->_class;
        $_primary_key = $class::primary_key();

        $class::addRelation('has_many', 'metadata', array(
            'key_from' => $_primary_key[0],
            'model_to' => 'Novius\Metadata\Model_Metadata',
            'key_to' => 'metadata_item_id',
            'cascade_save' => true,
            'cascade_delete' => true,
            'conditions' => array(
                'where' => array(
                    array('metadata_item_table', '=', \DB::expr(\DB::quote($class::table()))),
                ),
            ),
        ));

        $metadata_classes = $this->getMetadataClasses();
        foreach ($metadata_classes as $key => $metadata_class) {
            $nature = $metadata_class['nature'];
            $nature_model = is_array($nature) ? \Arr::get($nature, 'model', null) : $nature;
            $nature_pk = $nature_model::primary_key();

            $class::addRelation('many_many', 'metadata_'.$key, array(
                'table_through' => 'novius_metadata',
                'key_from' => $_primary_key[0],
                'key_through_from' => 'metadata_item_id',
                'key_through_to' => 'metadata_nature_id',
                'key_to' => $nature_pk[0],
                'cascade_save' => false,
                'cascade_delete' => false,
                'model_to'       => $nature_model,
                'where' => array(
                    array('metadata_item_table', '=', \DB::expr(\DB::quote($class::table()))),
                    array('metadata_class', '=', \DB::expr(\DB::quote($key))),
                ),
            ));
        }
    }

    public function getMetadataClasses()
    {
        $metadata_classes = \Config::get('novius_metadata::metadata_classes', array());

        foreach ($this->_properties['exclude'] as $metadata_class) {
            unset($metadata_classes[$metadata_class]);
        }

        $metadata_classes = \Arr::merge($metadata_classes, $this->_properties['classes']);

        return $metadata_classes;
    }

    public function crudConfig(&$config, $crud)
    {
        $metadata_classes = $this->getMetadataClasses();
        $label_metadata = __('Metadata');
        foreach ($metadata_classes as $key => $metadata_class) {
            $config['fields']['metadata_'.$key] = \Arr::merge(array(
                'renderer' => 'Novius\Metadata\Renderer_Metadata',
                'renderer_options' => array(
                    'metadata_class' => $metadata_class,
                ),
            ), \Arr::get($metadata_class, 'field'));

            foreach ($config['layout'] as $key_layout => $layout) {
                if ($layout['view'] === 'nos::form/layout_standard') {
                    \Arr::set($config['layout'][$key_layout], 'params.menu.'.$label_metadata, array('metadata_'.$key));
                }
            }
        }
    }
}
