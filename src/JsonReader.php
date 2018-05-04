<?php

namespace le0m\import;

use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Object;


/**
 *
 * @property-read array|false $columnNames Read-only column names, extracted from the source or passed as parameter to constructor; false to disable
 */
class JsonReader extends Object implements ReaderInterface
{
    /** @var null|array All elements read in memory */
    private $_elements = null;

    /** @var int Position of the current element in the file. */
    private $_position = 0;

    /**
     * @var bool|array If true, properties names will
     * be used as column names.
     * If an array, it will be used as names instead.
     * False to disable.
     */
    private $_columnNames = false;


    /**
     * {@inheritdoc}
     *
     * This class reads the whole file in memory, which may fail
     * for (very) big files.
     *
     * I didn't really need to import JSON files, so if performance is
     * crucial to you please implement this better and share back! :)
     *
     * @throws \yii\base\InvalidConfigException Problems with "file" property
     * @throws \yii\base\Exception Error reading the first element
     */
    public function __construct(array $config = [])
    {
        if (!isset($config['file'])) {
            throw new InvalidConfigException("Missing 'file' property; file path is required.");
        }

        $file = file_get_contents($config['file']);
        unset($config['file']);
        $this->_elements = json_decode($file, true);

        if (isset($config['columnNames'])) {
            $this->_columnNames = $config['columnNames'];
            unset($config['columnNames']);

            if ($this->_columnNames === true) {
                try {
                    $this->_columnNames = array_keys($this->_elements[0]);
                } catch (\Exception $ex) {
                    throw new Exception("Error reading first elemente, malformed file: ". $ex->getMessage());
                }
            }
        }

        // check if file is an array of elements (incremental 0-index)
        if ($this->_elements === []
            || array_keys($this->_elements) !== range(0, count($this->_elements) - 1)) {
            throw new InvalidConfigException("File is not an array of elements.");
        }

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnNames()
    {
        return $this->_columnNames;
    }

    /**
     * Return the current element (line) of the CSV file.
     *
     * @return array Indexed array of values
     */
    public function current()
    {
        return $this->_elements[$this->_position];
    }

    /**
     * Key of the current element (line).
     *
     * @return int
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * Move to the next element (line) in the CSV file.
     */
    public function next()
    {
        $this->_position++;
    }

    /**
     * Reset the file handler to the start of file,
     * getting the first element (line).
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * Check if current element (line) from CSV file is valid.
     *
     * @return bool
     */
    public function valid()
    {
        return isset($this->_elements[$this->_position]);
    }
}