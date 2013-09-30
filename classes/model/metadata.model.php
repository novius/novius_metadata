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

class Model_Metadata extends \Nos\Orm\Model
{
    protected static $_table_name = 'novius_metadata';
    protected static $_primary_key = array('metadata_id');

    protected static $_properties = array(
        'metadata_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'metadata_item_table' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'metadata_item_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
        ),
        'metadata_class' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'metadata_nature_table' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'metadata_nature_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
        ),
    );
}
