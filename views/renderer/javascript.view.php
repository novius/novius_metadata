<?php
/**
 * Metadata is an application for Novius OS for adding metadata on models.
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_metadata
 */

$label = \Arr::get($metadata_class, 'label');
$json = array(
    'input' => $field->name,
    'single' => $single,
    'url_picker' => 'admin/novius_metadata/picker?metadata_class_name='.urlencode($metadata_class_name).
        '&model='.urlencode(get_class($field->fieldset()->getInstance())),
    'texts' => array(
        'add' => \Arr::get($i18n, 'add', strtr(__('Add "{{metadata_class}}":'), array(
            '{{metadata_class}}' => $label,
        ))),
        'choose' => \Arr::get($i18n, 'choose', strtr(__('Choose a "{{metadata_class}}":'), array(
            '{{metadata_class}}' => $label,
        ))),
        'edit' => \Arr::get($i18n, 'edit', strtr(__('Edit "{{metadata_class}}":'), array(
            '{{metadata_class}}' => $label,
        ))),
        'remove' => \Arr::get($i18n, 'remove', strtr(__('Remove "{{metadata_class}}":'), array(
            '{{metadata_class}}' => $label,
        ))),
    )
);
?>
<script type="text/javascript">
    require(
        [
            'jquery-nos',
            'static/apps/novius_metadata/js/renderer.metadata.js',
            'link!static/apps/novius_metadata/css/renderer.css'
        ],
        function($) {
            var $metadata = $("#<?= $field->get_attribute('id') ?>").nosOnShow('one', function () {
                $metadata.renderermetadata(<?= \Format::forge($json)->to_json() ?>);
            });
        });
</script>
