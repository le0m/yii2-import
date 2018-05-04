Yii2 Importer
------------

This extension helps you import data from files like CSV or JSON into your application.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require le0m/yii2-import
```

or add

```
"le0m/yii2-import": "*"
```

to the require section of your composer.json.


Usage
-----

This extension provide support to import data from files to data structures like array or ActiveRecord.

First you must create an `Importer`, which needs a `reader` and an `importStrategy` to work, then you can import:

```php
$importer = new Importer([
    // this can be an array of strings to use as column names for the imported data,
    // `true` if the column names can be extracted from the file itself (the 'how' is specific to the implementation)
    // or `false` to disable and not use column names (default).
    'columnNames' => [...],
    
    // see below for how to setup this
    'reader' => [
        'class' => '\common\components\importer\CsvReader',
    ],
    
    // see below for how to setup this
    'importStrategy' => [
        'class' => '\common\components\importer\ARImportStrategy',
    ]
]);

// get the imported data
$data = $importer->import();
```

The importer will read the file using the chosen `reader` implementation, passing each element to the chosen `importStrategy` implementation. The returning value is an array of imported data.

If the `importStrategy` return `false` on a single element, the `Importer` will ignore it.

By default this extension provides two reader (CSV and JSON) and two import strategy (to array and to ActiveRecord) implementations to choose from, but you can add your own by implementing the appropriate interface.


**Import a CSV file**

The `CsvReader` accepts the following properties:

```php
'reader' => [
    'class' => '\common\components\importer\CsvReader',
    
    // limit the max length of a single line to read from file
    // 0 means no limit
    'lineLength' => 0,
    
    // character used to separate values on a single line
    'valueDelimiter' => ",",
    
    // character used to enclose values
    'valueEnclosure' => '"',
    
    // character used as escape in the values
    'escapeCharacter' => "\\",
]
```


**Import a JSON**

The `JsonImporter` has no particular properties:

```php
'reader' => [
    'class' => '\common\components\importer\JsonReader',
]
```


**Import to ActiveRecord**

The `ARImportStrategy` accepts the following properties:

```php
'importStrategy' => [
    'class' => '\common\components\importer\ARImportStrategy',
    
    // name of the ActiveRecord class to use for the import
    'className' => '...',
    
    // if you need custom code to handle loading data read from the file to the AR
    // (ex. column names from the file are different than property names from the AR
    // this function will be called for each element in the original file,
    // return false to not import the current element ([[Importer]] ignores false elements)
    'loadProperties' => function ($model, $data) { ... },
    
    // whether to automatically save the AR at the end of the import
    // AR with validation errors are returned anyway
    'saveRecord' => true,
]
```