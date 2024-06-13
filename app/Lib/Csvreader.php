<?php

namespace App\Lib;

class Csvreader
{

    var $fields;
    /** columns names retrieved after parsing */
    var $separator = ',';
    /** separator used to explode each line */
    var $enclosure = '"';
    /** enclosure used to decorate each field */

    var $max_row_size = 10000;
    /** maximum row size to be used for decoding */

    /**
     * Parse a file containing CSV formatted data.
     *
     * @access    public
     * @param    string
     * @param    boolean
     * @return    array
     */
    function parse_file($p_Filepath, $p_NamedFields = true)
    {
        $content = false;
        $file = fopen($p_Filepath, 'r');
        if ($p_NamedFields) {
            $this->fields = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure);
        }
        while (($row = fgetcsv($file, $this->max_row_size, $this->separator, $this->enclosure)) != false) {
            if ($row[0] != null) { // skip empty lines
                if (!$content) {
                    $content = array();
                }
                if ($p_NamedFields) {
                    $items = array();

                    // I prefer to fill the array with values of defined fields
                    foreach ($this->fields as $id => $field) {
                        if (isset($row[$id])) {
                            $items[$field] = $row[$id];
                        }
                    }
                    $content[] = $items;
                } else {
                    $content[] = $row;
                }
            }
        }
        fclose($file);
        return $content;
    }
}
