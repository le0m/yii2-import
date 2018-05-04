<?php

namespace le0m\import;

use yii\base\InvalidConfigException;
use yii\base\Object;
use Yii;


class Importer extends Object
{
    /**
     * @var ReaderInterface Configuration for reader component
     * Default to a CSV reader. Supply other implementations
     * depending on your needs.
     */
    public $reader = ['class' => '\common\components\importer\CsvReader'];

    /**
     * @var ImportStrategyInterface Configuration for import component
     * Default to a ActiveRecord importer. Supply other implementations
     * depending on your needs.
     */
    public $importStrategy = ['class' => '\common\components\importer\ArrayImportStrategy'];

    /**
     * @var bool|array If true, column names will be extracted
     * from the source (see specific implementation for details).
     * If an array, it will be used as column names instead.
     * False to disable.
     */
    public $columnNames = false;


    /**
     * {@inheritdoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (!isset($this->reader['class'])) {
            throw new InvalidConfigException("Reader component not configured, 'class' parameter missing");
        }

        if (!isset($this->importStrategy['class'])) {
            throw new InvalidConfigException("Import Strategy component not configured, 'class' parameter missing");
        }

        $this->reader['columnNames'] = $this->columnNames;
        $this->reader = Yii::createObject($this->reader);

        $this->importStrategy['columnNames'] = $this->reader->getColumnNames();
        $this->importStrategy = Yii::createObject($this->importStrategy);
    }

    /**
     * Import the whole source with the supplied strategy.
     *
     * @return array
     */
    public function import()
    {
        $res = [];

        foreach ($this->reader as $key => $item) {
            $imported = $this->importStrategy->import($item);

            if ($imported !== false) {
                $res[] = $imported;
            }
        }

        return $res;
    }
}