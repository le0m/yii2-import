<?php

namespace le0m\import;

use yii\base\Object;
use yii\base\InvalidConfigException;


class ARImportStrategy extends Object implements ImportStrategyInterface
{
    /**
     * @var string ActiveRecord class to use for the import
     */
    public $className;

    /**
     * @var bool Whether to save the record after import and
     * calling {@link $afterImport} or not
     */
    public $saveRecord = true;

    /**
     * @var \Closure|null Optional function to handle loading
     * property values in the AR model.
     *
     * This is useful if the column names differ from the AR
     * property names.
     * The signature of the function must match this:
     *
     * ```php
     * function ($model, $data) {}
     * ```
     *
     * where "$model" is the new AR instance and "$data" is
     * the imported values.
     */
    public $loadProperties;


    /**
     * @var array Optional column names
     */
    private $_columnNames = null;


    /**
     * {@inheritdoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        if (!isset($config['className'])) {
            throw new InvalidConfigException("ActiveRecord class name not set");
        }

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function setColumnNames($columns)
    {
        $this->_columnNames = $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnNames()
    {
        return $this->_columnNames;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function import($data)
    {
        if ($this->_columnNames === null) {
            throw new InvalidConfigException("Can't import, column names not set.");
        }

        $data = array_combine($this->_columnNames, $data);
        $class = $this->className;
        /** @var \yii\db\ActiveRecord $record */
        $record = new $class();

        if ($this->loadProperties instanceof \Closure) {
            $record = call_user_func_array($this->loadProperties, [$record, $data]);
        } else {
            $record->load($data, '');
        }

        if ($this->saveRecord) {
            // if validation fails, return the model anyway
            $record->save();
        }

        return $record;
    }
}