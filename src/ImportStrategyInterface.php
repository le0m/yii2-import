<?php

namespace le0m\import;


interface ImportStrategyInterface
{
    /**
     * Set optional column names.
     * If set, values are supposed to follow column
     * order through all the source.
     *
     * @param array $columns
     */
    public function setColumnNames($columns);

    /**
     * Get column names, if set.
     * If set, values are supposed to follow column
     * order through all the source.
     *
     * @return mixed
     */
    public function getColumnNames();

    /**
     * Import the data to the implemented format.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function import($data);
}