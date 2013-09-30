/**
 * Metadata is an application for Novius OS for adding metadata on models.
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_metadata
 */

CREATE TABLE IF NOT EXISTS `novius_metadata` (
  `metadata_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `metadata_item_table` varchar(255) NOT NULL,
  `metadata_item_id` int(10) unsigned NOT NULL,
  `metadata_class` varchar(30) NOT NULL,
  `metadata_nature_table` varchar(255) NOT NULL,
  `metadata_nature_id` int(11) NOT NULL,
  PRIMARY KEY (`metadata_id`),
  KEY `metadata_item_table` (`metadata_item_table`,`metadata_item_id`),
  KEY `metadata_nature_table` (`metadata_nature_table`,`metadata_nature_id`),
  KEY `metadata_class` (`metadata_class`)
) DEFAULT CHARSET=utf8;