# Metadata for Novius OS

Metadata is an application for Novius OS for adding metadata on models.

## Requirements

* The Metadata applications run on Novius OS Chiba 2 and upper.

## Installation

* [How to install a Novius OS application](http://community.novius-os.org/how-to-install-a-nos-app.html)

## Documentation

After you installed this application in your Novius OS, you will be able to define metadata classes and which models use these metadata.
 CRUD interface of these models will be automatically updated to add metadata selection.

### Configuration of metadata classes

Extend the config file `metadata_classes` and define classes.

```php
return array(
    'metadata_class_name' => array(
        // Use as field label
        'label' => __('Metadata class label'),

        // The model of the metadata class's nature
        'nature' => 'Model_Nature',

        // The field configuration used in the CRUD
        'field' => array(
            // By default, field uses the renderer Novius\Metadata\Renderer_Metadata
            'renderer_options' => array(
                // Specify if you want just one metadata for this class
                'single' => false, // True by default

                // If count of nature items is under this number, the renderer will be just a select
                'select_limit' => 50, // 50 by default

                // Translations for different labels in the renderer
                'i18n' => array(
                    'add' => __('Add items'),
                    'choose' => __('Choose an item'),
                    'remove' => __('Remove item'),
                    'edit' => __('Edit item'),
                ),
            ),
        ),
    ),

    'other_metadata_class_name' => array(
        'label' => __('Other Metadata class label'),

        // Nature can be an array with two keys: model and query
        'nature' => array(
            'model' => 'Model_Nature',

            // Additional query on the nature model
            'query' => array(
                'where' => array(
                //...
                ),
            ),
        ),

        'field' => array(
            // A specific renderer for this metadata class.
            // The field's name will be the concatenation of 'metadata_' and the class name
            'renderer' => 'Renderer_Metadata_Class',
            // The renderer_options array will automatically contain a key 'metadata_class'
            // The value of this key is the metadata class config array
            'renderer_options' => array(),
        ),
    ),
);
```

### Behaviour Novius\Metadata\Behaviour_Hasmetadata

Metadata will be added on all models that implement this behaviour.

```php
protected static $_behaviours = array(
    'Novius\Metadata\Behaviour_Hasmetadata' => array(
        // List of metadata classes that you want to exclude for this model
        //'exclude' => array(),

        // List of specific metadata classes. See above metadata classes' configuration.
        //'classes' => array(),
    ),
),
```

Relations added automatically:

* metadata : has_many relation to Novius\Metadata\Model_Metadata.
* metadata_`class_name` : many_many relation to the nature model of the metadata class.

## License

Licensed under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.