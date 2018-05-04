<?php

namespace le0m\import;

use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Object;


/**
 *
 * @property-read array|false $columnNames Read-only column names, extracted from the source or passed as parameter to constructor; false to disable
 */
class CsvReader extends Object implements ReaderInterface
{
    /** @var int Max character length of a single line. */
    public $lineLength = 0;

    /** @var string Character used to delimit values. */
    public $valueDelimiter = ",";

    /** @var string Character used to enclose string values. */
    public $valueEnclosure = '"';

    /** @var string Escape character used in values. */
    public $escapeCharacter = "\\";


    /** @var null|bool|resource Reference to the opened file handle. */
    private $_fileHandle = null;

    /** @var null|array Current element returned on each iteration. */
    private $_currentElement = null;

    /** @var int Position of the current element (line) in the file. */
    private $_position = 0;

    /**
     * @var int First line of elements in the file. Used to skik the
     * first line if it contains the column names.
     */
    private $_firstLine = 0;

    /**
     * @var bool|array If true, first line of the CSV file
     * will be used as column names.
     * If an array, it will be used as names instead.
     * False to disable.
     */
    private $_columnNames = false;


    /**
     * {@inheritdoc}
     *
     * @throws \yii\base\InvalidConfigException Error with "file" property
     * @throws \yii\base\Exception Error opening file
     */
    public function __construct($config = [])
    {
        if (!isset($config['file'])) {
            throw new InvalidConfigException("Missing 'file' property; file path is required.");
        }

        try{
            $this->_fileHandle = fopen($config['file'], 'rb');
            unset($config['file']);

            if (isset($config['columnNames'])) {
                $this->_columnNames = $config['columnNames'];
                unset($config['columnNames']);

                if ($this->_columnNames === true) {
                    $this->_columnNames = $this->readLine();
                    $this->_position++;
                    $this->_firstLine = 1;
                }
            }
        } catch (\Exception $ex) {
            throw new Exception("Error opening file: " . $ex->getMessage());
        }

        parent::__construct($config);
    }

    /**
     * Read a line of the CSV file.
     *
     * @return array|false Indexed array of values; false on error or EOF
     */
    protected function readLine()
    {
        return fgetcsv($this->_fileHandle, $this->lineLength, $this->valueDelimiter, $this->valueEnclosure, $this->escapeCharacter);
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
        return $this->_currentElement;
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
        $this->_currentElement = $this->readLine();
        $this->_position++;
    }

    /**
     * Reset the file handler to the start of file,
     * getting the first element (line).
     */
    public function rewind()
    {
        rewind($this->_fileHandle);
        $this->_currentElement = $this->readLine();
        $this->_position = $this->_firstLine;

        if ($this->_firstLine === 1) {
            // first line are column names, read forward
            $this->_currentElement = $this->readLine();
        }
    }

    /**
     * Check if current element (line) from CSV file is valid.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->_currentElement !== false;
    }
}