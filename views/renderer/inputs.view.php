<?php
/**
 * Metadata is an application for Novius OS for adding metadata on models.
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_metadata
 */
?>
<div id="<?= $field->get_attribute('id') ?>">
<?php
$item = $field->fieldset()->getInstance();
foreach ($item->{$field->name} as $id => $nature) {
    echo '<input type="hidden" name="', $field->name, $single ? '' : '[]', '" value="', $id,
        '" data-title="', e($nature->title_item()), '" />';
}
?>
</div>