<?php

namespace le0m\import;


interface ReaderInterface extends \Iterator
{
    /**
     * Get column names for the imported elements.
     *
     * This information can be obtained from the user,
     * or by reading the file in most formats.
     *
     * Refer to the specific implementation for actions
     * to be taken.
     *
     * @return array
     */
    public function getColumnNames();
}