<?php

namespace le0m\import;

use yii\base\Object;


class ArrayImportStrategy extends Object implements ImportStrategyInterface
{
    /** @var array Optional column names */
    private $_columnNames = null;


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
     */
    public function import($data)
    {
        if ($this->_columnNames !== null) {
            $data = array_combine($this->_columnNames, $data);
        }

        return $data;
    }
}