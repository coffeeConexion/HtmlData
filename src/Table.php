<?php
/**
 * Creates a formatted HTML table.
 *
 * This creates HTML tables dynamically from arrays. This is useful for
 * managing dynamically-generated content, such as data from a CSV file or
 * database query. Additionally, you can pass nested arrays using a specified
 * key for the text and remaining keys as HTML attribute-value pairs. This
 * makes it easy to set id, class, style, or any arbitrary attribute for a
 * given element: cell, row, column, thead, table, etc.  as an HTML table or
 * reading data from a CSV file, database . It easy enough to do this by
 * looping through an array of database output, but this class simplifies
 * the process and standardizes the format.
 *
 * # Elements
 * The following elements may be set:
 * * table
 * * tbody
 * * thead
 * * tfoot
 * * caption
 * * colgroup/Col
 * * tr
 * * td
 * * th
 *
 * # Additional features:
 * The indent() meothd sets indentation dynamically in the source code. This
 * may be help to make source code more readable.
 *
 * The setColumnOrder() method sets the left-to-right order of columns. When
 * in use any td element that is not specified will be ignored.
 *
 * The rowCounter() method adds a column (at left) with the row number
 * contained in the td element. This works as a simple row index in the
 * formatted table.
 *
 * ***
 *
 * Created: May 22, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */

namespace CoffeeConexion\HtmlData;

class Table
{

    /**
     * String or integer key for the array element of the td element text.
     * @var mixed
     */
    private $textKey = 'text';

    /**
     * Array of keys identifying the td elements in the tr array.
     * @var array
     */
    private $colKeys = array();

    /**
     * When true, uses empty td cell when element from colKeys property not in row.
     * @var bool
     */
    private $allowMissing = true;

    /**
     * Placeholder text when element from colKeys property not in row.
     * @var string
     */
    private $missingCellText = '';
    /**
     * Array of keys identifying the attribute elements the tr array.
     * @var array
     */
    private $rowAttrKeys = array();

    /**
     * The (whitespace) string to use for indentation in the source code.
     * @var string
     */
    private $indentString = "    ";

    /**
     * The number of "units" of whitespace to prepend to HTML elements.
     * @var int
     */
    private $indentNum = 0;

    /**
     * An optional row counter in the first column (0 = not in use).
     * @var int
     */
    private $rowCounter = 0;

    /**
     * The text for the column header of the optional counter column.
     * @var string
     */
    private $rowCounterHeader = '#';

    /**
     * The array of attributes for the HTML col attributes.
     * @var array
     */
    private $colAttributes = array();

    /**
     * Array of integer values to set the widths of each column in order; optional (if not set, the browser manages the column widths).
     * @var array
     */
    private $columnWidthArray = array();

    /**
     * Displays column widths in pixels instead of percentages when true.
     * @var bool
     */
    private $colPixel = false;

    /**
     * A formatted HTML caption element.
     * @var string
     */
    private $captionString = '';

    /**
     * A string of HTML attributes for the table element.
     * @var string
     */
    private $tableAttrString = '';

    /**
     * A string of HTML attributes for the tbody element.
     * @var string
     */
    private $tbodyAttrString = '';

    /**
     * A formatted HTML thead element.
     * @var string
     */
    private $theadString = '';

    /**
     * A formatted HTML thead element.
     * @var string
     */
    private $tfootString = '';


    /**
     * Creates a full HTML table.
     *
     * This returns a complete HTML table as a string, with all elements including:
     * * Table header
     * * Table body, with rows
     * * Table footer
     * @param array $rows An array table rows, each containing an array of td values.
     * @return string A complete HMTL table.
     * @throws Exception if either parameter is not array.
     */
    public function table($rows)
    {
        if (!is_array($rows)) {
            $msg = 'Expecting array for row parameter, ' . gettype($rows) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Creates the opening tag for the table element
        $tableString = str_repeat($this->indentString, $this->indentNum) . "<table" . $this->tableAttrString . ">\n";

        $prebodyElements = [
            $this->captionString,
            $this->formatCols(),
            $this->theadString,
            $this->tfootString
        ];

        foreach ($prebodyElements as $v) {
            if (!empty($v)) {
                $tableString .= $v . "\n";
            }
        }

        // Appends the table body element
        $tableString .= str_repeat($this->indentString, $this->indentNum) . '<tbody' . $this->tbodyAttrString . ">\n";

        // Appends the formatted string for each tr element
        foreach ($rows as $r) {
            $tableString .= $this->formatTr($r);
        }

        // Closes the table body
        $tableString .= str_repeat($this->indentString, $this->indentNum) . '</tbody>' . "\n";

        // Closes and return the table element
        return $tableString . str_repeat($this->indentString, $this->indentNum) . '</table>' . "\n";
    }

    /**
     * Sets the HTML table element attributes.
     *
     * @param array A 1-dimensional array of HTML attributes=>values for the table element.
     */
    public function setTableAttr(array $attributes)
    {
        $this->tableAttrString = $this->formatAttr($attributes);
    }

    /**
     * Sets the HTML tbody element attributes.
     *
     * @param array A 1-dimensional array of HTML attributes=>values for the tbody element.
     */
    public function setTbodyAttr(array $attributes)
    {
        $this->tbodyAttrString = $this->formatAttr($attributes);
    }

    /**
     * Sets a formatted HTML table header (thead) element.
     *
     * @param array $columns The column header (th) elements.
     * @param array $attr The array of key=>value pairs for the thead element.
     */
    public function setThead($columns, $attr = null)
    {
        // The string to return
        $theadString = str_repeat($this->indentString, $this->indentNum) . '<thead';

        // If set, format the attributes for the thead element
        if (!is_null($attr)) {
            $theadString .= $this->formatAttr($attr);
        }

        // Closse the thead element and start the row element
        $theadString .= ">\n" . str_repeat($this->indentString, $this->indentNum + 1) . "<tr>\n";

        // Ready the columns array for processing
        $columns = $this->standardizeHeader($columns);

        // Loops through the columns array
        foreach ($columns as $c) {
            $theadString .= str_repeat($this->indentString, $this->indentNum + 2) . $this->formatTh($c);
        }

        $theadString .= str_repeat($this->indentString, $this->indentNum + 1) . "</tr>\n";

        $this->theadString = $theadString . str_repeat($this->indentString, $this->indentNum) . '</thead>';
    }

    /**
     * Sets a formatted HTML table footer (tfoot) element.
     *
     * @param array $columns The column header (td) elements.
     * @param array $attr The array of key=>value pairs for the thead element.
     */
    public function setTfoot($columns, $attr = null)
    {
        // The string to return
        $tfootString = str_repeat($this->indentString, $this->indentNum) . '<tfoot';

        // If set, formats the attributes for the thead element
        if (!is_null($attr)) {
            $tfootString .= $this->formatAttr($attr);
        }

        // Close the thead element and start the row element
        $tfootString .= ">\n" . str_repeat($this->indentString, $this->indentNum + 1) . "<tr>\n";

        // Loops through the columns array
        foreach ($columns as $c) {
            $tfootString .= $this->formatTd($c) . "\n";
        }

        // Closes the tr element
        $tfootString .= str_repeat($this->indentString, $this->indentNum + 1) . "</tr>\n";

        // Closes the tfoot element
        $this->tfootString = $tfootString . str_repeat($this->indentString, $this->indentNum) . "</tfoot>";
    }

    /**
     * Sets a formatted HTML table caption element.
     *
     * @param string $caption The text of the caption element.
     * @param array $attr An array HTML attribute=>value pairs.
     * @throws Exception if $caption is not scalar or if $attr is not empty and not a valid array.
     */
    public function setCaption($caption, $attr = null)
    {
        // If no caption text is provided, set to the empty string
        if (empty($caption)) {
            $this->captionString = '';
            return;
        }

        if (!is_string($caption)) {
            $msg = 'Expecting string for caption, ' . gettype($caption) . ' given.';
            throw new \Exception($msg, 1);
        }

        // If set, tests type of $attr
        if (!is_null($attr) && !is_array($attr)) {
            $msg = 'Expecting array for attribute parameter, ' . gettype($attr) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Creates the formatted attribute string
        $attrString = '';
        if (!empty($attr)) {
            $attrString = $this->formatAttr($attr);
        }

        // Creates the formatted HTML element
        $this->captionString = str_repeat($this->indentString, $this->indentNum) . '<caption' . $attrString . '>' . $caption . '</caption>';
    }

    /**
     * Sets the array of HTML col element (within a colgroup element).
     *
     * @param array $cols Array of HTML attribute=>value pairs.
     * @throws Exception if $cols is not array or does not contain arrays of attribute=>value pairs.
     */
    public function setHtmlCols($cols)
    {
        if (!is_array($cols)) {
            $msg = 'Expecting array, ' . gettype($cols) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Validates the attribute=>value pairs for each col element
        foreach ($cols as $c) {
            $this->validateAttr($c);
        }
        $this->colAttributes = $cols;
    }

    /**
     * Returns formatted HTML for col elements.
     *
     * @return string Formatted HTML colgroup contain col elements.
     */
    private function formatCols()
    {
        $colArray = $this->colAttributes;

        // If the width array has been set, pushes width attribute to columns
        if (!empty($this->columnWidthArray)) {
            $colArray = $this->mergeWidthToCols($colArray);
        }

        // Return if no col elements
        if (empty($colArray)) {
            return;
        }

        $s = "<colgroup>\n";

        // Format the col elements with attributes
        foreach ($colArray as $v) {
            $s .= str_repeat($this->indentString, $this->indentNum + 1) .  '<col' . $this->formatAttr($v) . " />\n";
        }

        return $s . "</colgroup>";
    }

    /**
     * Sets the width values into the attribute array.
     *
     * This method references the columnWidthArray property and sets those values as the CSS width for the HTML style attribute. If the style attribute is already set, the width is prepended.
     * @param array $headerArray The array th values in the tr elements under thead.
     * @return array A formatted array with the width assigned to the style value for each attribute.
     */
    private function mergeWidthToCols($colArray)
    {
        $wArray = $this->columnWidthArray;

        // Get the difference in size between the column and width arrays
        $diff = count($wArray) - count($colArray);

        // Pads the column array if width array has more elements
        if ($diff > 0) {
            $a = array_fill(0, $diff, array());

            // Merge the placeholder columns to $colArray
            // (Note: could use array_fill here but this preserves existing order for string keys)
            $colArray = array_merge($a);
        }

        // Use percentage by default
        $unit = '%';
        if ($this->colPixel) {
            // When using pixels
            $unit = 'px';
        }

        // Sets the width attributes for each columns
        foreach ($colArray as $k => $c) {

            // Removes first width element from $wArray and formats as CSS rule
            $newStyle = 'width: ' . array_shift($wArray) . $unit . ';';

            // If style element is already set, append existing style rules
            if (isset($c['style'])) {
                $newStyle .= ' ' . $c['style'];
            }
            $colArray[$k]['style'] = $newStyle;
        }

        return $colArray;
    }

    /**
     * Sets the textKey property, the key for the text in array elements.
     *
     * The HTML td element contains only a single string value, but may also contain attributes. When an array is passed for the to an element using an array with one value the text for that element. The $key is the array key for the text; all other elements are assumed to be attribute=>value pairs.
     *
     * @param mixed $key The string or integer key for the HTML td element text.
     * @throws Exception if $key is neither string nor integer.
     */
    public function setTextKey($key)
    {
        if (!is_string($key) && !is_integer($key)) {
            $msg = 'Expecting string or integer, ' . gettype($key) . ' given.';
            throw new \Exception($msg, 1);
        }

        $this->textKey = $key;
    }

    /**
     * Sets the colKeys property, which orders td elements in a tr element (horizontally).
     *
     * By default the table will generate HTML td elements in the order of the source array. By setting the colKeys property, the table will order td elements in a specified order (within the row), regardless of the order of keys in the source array. Furthermore, by setting this property, any data elements not included in the colKeys that would be formatted as td elements will be ignored completely. This makes it easy to select a subset of data by specifying columns (by key).
     * @param array $keys The keys identifying the td value.
     * @param bool $allowMissing True = use empty cell when the key does not exist.
     * @param string $missingCellText Placeholder text to use for cells missing when row missing colKeys element.
     * @throws Exception if $keys contains any non-scalar values, or (downstream) if any row in $key does not contain $key.
     */
    public function setColumnOrder(array $keys, $allowMissing = true, $missingCellText = null)
    {
        // Tests that all values scalar
        foreach ($keys as $k => $v) {
            if (!is_scalar($v)) {
                $msg = 'Expecting scalar value for ' . $k . ', ' . gettype($v) . ' given.';
                throw new \Exception($msg, 1);
            }
        }
        $this->colKeys = $keys;

        // Turns off allowMissing property (throws exception when key is missing)
        if (empty($allowMissing)) {
            $this->allowMissing = false;
        }

        if (is_string($missingCellText)) {
            $this->missingCellText = $missingCellText;
        }
    }

    /**
     * Sets the rowAttrKeys property, which identifies elements to format as attributes instead of cell data.
     *
     * @param array $keys The keys identifying the attributes.
     * @throws Exception if $keys contains any non-scalar values.
     */
    public function setRowAttrKeys(array $keys)
    {
        // Test that all values scalar
        foreach ($keys as $k => $v) {
            if (!is_scalar($v)) {
                $msg = 'Expecting scalar value for ' . $k . ', ' . gettype($v) . ' given.';
                throw new \Exception($msg, 1);
            }
        }
        $this->rowAttrKeys = $keys;
    }

    /**
     * Formats an HTML th element.
     *
     * @param mixed $th A scalar value for the header text or a valid array for the text and HTML attributes.
     * @return string A formatted HTML th element.
     * @throws Exception if $th is not scalar or a valid array.
     */
    private function formatTh($th)
    {
        // For scalar headers, format and return the HTML th element
        if (is_scalar($th)) {
            return '<th>' . (string) $th . "</th>\n";
        }

        // Throws exception if $th is neither scalar nor array
        if (!is_array($th)) {
            $msg = 'Expecting scalar or array for th element, ' . gettype($th) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Sets the text value and unset the element
        $text = $th[$this->textKey];
        unset($th[$this->textKey]);

        // Returns the formatted th element (with attributes)
        return '<th' . $this->formatAttr($th) . ">$text</th>\n";
    }

    /**
     * Creates a formatted header array for processing.
     *
     * The column header formatted uses a multidimentional array, with the key for the text for the column header identified with the textKey property. This method converts scalar column header values to formatted arrays, and ensures that array values are have a key for the text.
     * @param array $headerArray The array of column headers.
     * @return array A multidimensional array for column headers and their attributes.
     * @throws Exception if element is not scalar or array, or if array does not have a key matching the textKey property.
     */
    private function standardizeHeader($headerArray)
    {
        // Sets the counter for column elements
        $i = 0;

        // Array to return
        $formattedArray = array();

        // Creates a column header for the row counter
        if ($this->rowCounter) {
            // Sets the array element for the text
            $formattedArray[$i][$this->textKey] = $this->rowCounterHeader;
            $i++;
        }

        // Loop through the column
        foreach ($headerArray as $columnName => $th) {

            // Validate the data types
            if (!is_scalar($th) && !is_array($th)) {
                $msg = 'Expecting scalar or array for the HTML th element, ' . gettype($th) . ' given.';
                throw new \Exception($msg, 1);
            }

            // If the th element is scalar, make it the key and push an empty array
            if (is_scalar($th)) {
                // Create the array for this header
                $formattedArray[$i] = array();
                // Push the text value
                $formattedArray[$i][$this->textKey] = $th;
            }

            // Test the text value
            if (is_array($th)) {

                if (!array_key_exists($this->textKey, $th)) {
                    $msg = 'Could not find a text value for key ' . $columnName . '.';
                    throw new \Exception($msg, 1);
                }

                if (!is_scalar($th[$this->textKey])) {
                    $msg = 'Expecting string or integer for column header text key, ' . gettype($th) . ' given.';
                    throw new \Exception($msg, 1);
                }

                // Push the array for this column
                $formattedArray[$i] = $th;
            } // End of is_array() if

            $i++;
        } // End of $headerArray foreach

        return $formattedArray;
    }

    /**
     * Adds the row counter column.
     *
     * @param mixed Integer or numeric string for row count starting number.
     * @param string The text for the column header (uses '#' by default).
     */
    public function rowCounter($startNum = 1, $text = null)
    {
        // Sets properties to default if counter row not in use
        if (empty($startNum)) {
            $this->rowCounter = 0;
            return;
        }

        // Tests the data type
        if (!is_int($startNum) && !is_numeric($startNum)) {
            $msg = 'Expecting integer or numeric string for starting number, ' . gettype($startNum) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Typecasts as integer in case numeric string passed
        $startNum = (int) $startNum;

        // Uses zero (no row counter) if negative number provided
        if ($startNum < 0) {
            $startNum = 0;
        }

        $this->rowCounter = $startNum;

        // No text parameter passed
        if (is_null($text)) {
            return;
        }

        if (!is_scalar($text)) {
            $msg = 'Expecting scalar value for column header, ' . gettype($text) . ' given.';
            throw new \Exception($msg, 1);
        }
        $text = (string) $text;
        $this->rowCounterHeader = $text;
    }

    /**
     * Sets indentation for the HTML elements.
     *
     * Indentation can greatly facilitate reading HTML source code. Indentation is based on the two factors:
     * * * The level: number of units of whites to prepend (if all elements are indented)
     * * * The unit: is the string of whitespace characters; default is 4 spaces (0x20)
     * The use no identation for any elemetns, set the whitespace to the empty string ('').
     *
     * @param integer $num The identation "level" to start from (if the entire table is indented).
     * @param string $whitespace The character string "unit" to use for indentation.
     * @throws Exception either paramter is the wrong type.
     */
    public function indent($num = 0, $whitespace = '    ')
    {
        // Tests the $num type and range
        if (!is_integer($num) && !is_numeric($num)) {
            $msg = 'Expecting integer or numeric string for number of indentations, ' . gettype($num) . ' given.';
            throw new \Exception($msg, 1);
        }
        $this->indentNum = (int) $num;

        if (!is_string($whitespace)) {
            $msg = 'Expecting string for whitespace parameter, ' . gettype($whitespace) . ' given.';
            throw new \Exception($msg, 1);
        }

        $this->indentString = $whitespace;
    }

    /**
     * Sets the array of width for all columns.
     *
     * Tests whether each element is an integer.  If all values are valid, sets DataTable::columnWidthArray to this array.
     * @param array $columnWidthArray Array of integers, each referring to the column width in pixels
     * @param boolean $percent true = use percentages for column width; default false is to use pixels.
     * @throws Exception if any value in $columnWidthArray is not an integer.
     */
    public function colWd(array $columnWidthArray, $percent = false)
    {
        // Tests that each value is a positive integer
        foreach ($columnWidthArray as $key => $w) {

            // If any element is not a positive integer, throw an exception with the element's key
            if (!is_integer($w) || $w < 1) {
                $errorMessage = "All column width values must be positive integers. See element for key '$key'.";
                throw new \Exception($errorMessage);
            }
        }

        // If there were no non-integer values, sets this array as the columnWidthArray
        $this->columnWidthArray = $columnWidthArray;

        // If $percent evaluates to true, use pixels; otherwise percentage widths are assumed.
        if ($percent == true) {
            $this->colPixel = true;
        }
    }

    /**
     * Validates the HTML attribute array.
     *
     * The attribute array uses a key=>value pair corresponding to the attribute="value" pair in an HTML element. Valid attribute names and values in HTML (and in SGML) have specific requirements; this is just a quick data type validation.
     * @param array $array An associative array.
     * @param boolean $name true = validate that the key is non-numeric (proper HTML attribute name).
     * @return boolean Returns true if all keys and values in the array pass type validation.
     * @throws Exception if value is not scalar, or if key (HTML attribute) is integer or numeric string.
     */
    private function validateAttr(array $array, $name = false)
    {
        // If $name is true, tests that the key is a valid (non-numeric) HTML attribute name.
        if ($name) {

            // Test each key and value for valid HTML characters (allows underscore but other)
            foreach ($array as $k => $v) {
                if (is_integer($k) || is_numeric($k)) {
                    $msg = 'Expecting valid HTML attribute name, ' . $k . ' given.';
                    throw new \Exception($msg, 1);
                }
            }
        } // End of $name if

        // Tests that each value is scalar.
        foreach ($array as $k => $v) {

            if (!is_scalar($v)) {
                $msg = 'Expecting string or integer for ' . $k . ', ' . gettype($v) . ' given.';
                throw new \Exception($msg, 1);
            }
        }

        return true;
    }

    /**
     * Creates a formatted string of HTML attributes for an HMTL element.
     *
     * This method provides mapping of values in table columns to HTML attributes. Because the attribute names are passed as arguments, it allows typical HTML attributes (id, class, style, etc.) as well as custom attributes (for use with JavaScript, etc.). The $ignore array contains the keys in the array that should not be formatted as attributes; this is used for the text and value keys, and any added on the fly.
     *
     * @param array $attributes A "row" of data with the option text, value, and possibly attribute values, generally from a table.
     * @param array $ignore An array keys to ignore (optional).
     * @return string The formatted HTML attribute text.
     * @throws Exception if value in $ignore does not correspond to a key in $attributes.
     */
    private function formatAttr(array $attributes, array $ignore = null)
    {
        // Validates the array of attributes
        $this->validateAttr($attributes, true);

        // If $ignore is set, removes from $attributes
        if (!empty($ignore)) {
            $this->validateAttr($ignore);

            foreach ($ignore AS $i) {

                if (!array_key_exists($i, $attributes)) {
                    $msg = 'Could not find key ' . $i . ' in attribute array.';
                    throw new \Exception($msg, 1);
                }
                unset($attributes[$i]);
            }
        } // End of $ignore if

        // Attribute string
        $attrString = '';

        // Loops through the array to create attributes.
        foreach ($attributes as $k => $v) {
            $attrString .= ' ' . $k . '="' . $v . '"';
        }

        return $attrString;
    }

    /**
     * Creates an HTML td (table data) element.
     *
     * For scalar and null values, this is simple matter of formatting the element. For arrays, the text value must be identified, and the rest formatted as HTML attribute=>value pairs.
     * @param mixed $tdData A scalar or null value for the td element text, or an array containing text and element attributes.
     * @return string A formatted HTML td element.
     * @throws Exception if $tdData is not scalar or null (array in use), and the textKey property has not been set.
     */
    private function formatTd($tdData)
    {
        // Scalar and null values require no attribute formatting
        if (is_scalar($tdData) || is_null($tdData)) {
            return "<td>$tdData</td>";
        }

        // If any other type is passed, throw exception
        if (!is_array($tdData)) {
            $msg = 'Expecting scalar, null, or array for table data, ' . gettype($tdData) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Tests that the property with the text identifier is set
        if (empty($this->textKey)) {
            $msg = 'The text identifier for the HTML td element must be set to use an array. Set using the setTextKey method.';
            throw new \Exception($msg, 1);
        }

        $attributes = $this->formatAttr($tdData, array($this->textKey));
        $text = $tdData[$this->textKey];

        return '<td' . $attributes . ">$text</td>";
    }

    /**
     * Formats the tr element and all child td elements.
     *
     * @param array $trData The array of td elements and any tr attributes.
     * @return string A formatted HTML tr element.
     */
    public function formatTr(array $trData)
    {
        // Array of row attributes
        $attr = array();
        $this->extractRowAttr($trData, $attr);

        // Array of formatted td elements
        $cellArray = $this->formatCells($trData);

        // Opening tr tag
        $trString = str_repeat($this->indentString, $this->indentNum +1) . '<tr' . $this->formatAttr($attr) . ">\n";

        // Appends each td element
        foreach ($cellArray as $k => $cell) {
            $trString .= str_repeat($this->indentString, $this->indentNum + 2) . $cell ."\n";
        }

        // Closing tr tag
        $trString .= str_repeat($this->indentString, $this->indentNum + 1) . "</tr>\n";
        return $trString;
    }

    /**
     * Removes moves row attribute elements to the attribute array.
     *
     * For any key in $row matching a value in the rowAttrKeys property, remove that element from $row and push onto $attr. Both arrays are passed by reference.
     * @param array $tdData The array containing all data for the row.
     * @param array $attr The array of HTML attribute=>value pairs.
     */
    private function extractRowAttr(&$row, &$attr)
    {
        // If this feature is not in use
        if (empty($this->rowAttrKeys)) {
            return;
        }

        // Pulls out attribute data
        foreach($this->rowAttrKeys as $k => $v) {

            // Sets values to the row attributes array, unsets from the row (cell) values
            if (array_key_exists($v, $row)) {
                $attr[$k] = $row[$v];
                unset($row[$v]);
            }
        }
    }

    /**
     * Formats HTML td cells.
     *
     * @param array $row The array of cells for this row.
     * @return array An array of formatted HTML td elements.
     * @throws Exception if allowMissing property is false and no column found.
     */
    private function formatCells($row)
    {
        // The array of formatted HTML td elements to return
        $cells = array();

        // If using a row counter column
        if ($this->rowCounter > 0) {
            $cells[] = '<td>' . $this->rowCounter . "</td>";
            $this->rowCounter++;
        }

        // If not using the colKeys feature, loops through values in order in array
        if (empty($this->colKeys)) {

            foreach ($row as $k => $v) {
                $cells[] = $this->formatTd($v);
            }
            return $cells;
        }

        // If using the setColumnOrder method
        foreach ($this->colKeys as $k => $v) {

            // Throws exception if key does not exist and missing keys not allowed
            if (!array_key_exists($v, $row) && !$this->allowMissing) {
                $msg = "No column '" . $v . "' found for row. Check values in setColumnOrder method.";
                throw new \Exception($msg, 1);
            }

            // If the key exists, pushes formats td element and pushes to $cells
            if (array_key_exists($v, $row)) {
                $cells[] = $this->formatTd($row[$v]);
            } else {
                $cells[] = $this->formatTd($this->missingCellText);
            }
        }
        return $cells;
    }
}
