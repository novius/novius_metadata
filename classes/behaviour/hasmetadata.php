<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
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
        I18n::current_dictionary('novius_metadata::common');
        \Config::load('novius_metadata::classes', true);
    }

    /**
     * Add relations for linked media and wysiwyg shared with other context
     */
    public function buildRelations()
    {
        $class = $this->_class;

        $class::addRelation('has_many', 'metadata', array(
            'key_from' => $class::primary_key(),
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
    }

    protected function _getMetadataClasses()
    {
        $metadata_classes = \Config::get('novius_metadata::classes', array());

        foreach ($this->_properties['exclude'] as $metadata_class) {
            unset($metadata_classes[$metadata_class]);
        }

        $metadata_classes = \Arr::merge($metadata_classes, $this->_properties['classes']);

        return $metadata_classes;
    }

    public function crudConfig(&$config, $crud)
    {
        $metadata_classes = $this->_getMetadataClasses();
        $label_metadata = __('Metadata');
        foreach ($metadata_classes as $key => $metadata_class) {
            $config['fields']['metadata_'.$key] = array_merge(array(
                'label' => \Arr::get($metadata_class, 'label', $key),
            ), \Arr::get($metadata_class, 'field'));

            foreach ($config['layout'] as $key_layout => $layout) {
                if ($layout['view'] === 'nos::form/layout_standard') {
                    \Arr::set($config['layout'][$key_layout], 'params.menu.'.$label_metadata, array('metadata_'.$key));

                }
            }
        }
    }
}
