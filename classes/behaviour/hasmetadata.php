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
    protected $_properties = array();

    public function __construct($class)
    {
        parent::__construct($class);
        /*if (!isset($this->_properties['common_fields'])) {
            $this->_properties['common_fields'] = array();
        }*/
    }

    /**
     * Add relations for linked media and wysiwyg shared with other context
     */
    public function buildRelations()
    {
        /*$class = $this->_class;

        $class::addRelation('has_many', 'linked_shared_wysiwygs_context', array(
            'key_from' => $this->_properties['common_id_property'],
            'model_to' => 'Nos\Model_Wysiwyg',
            'key_to' => 'wysiwyg_foreign_context_common_id',
            'cascade_save' => true,
            'cascade_delete' => true,
            'conditions' => array(
                'where' => array(
                    array('wysiwyg_join_table', '=', \DB::expr(\DB::quote($class::table()))),
                ),
            ),
        ));*/
    }

    public function crudConfig(&$config, $crud)
    {

    }
}
