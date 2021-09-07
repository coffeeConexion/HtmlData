<?php
/**
 * Creates an HTML selection list.
 *
 * ***
 *
 * Created: May 22, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */

namespace CoffeeConexion\HtmlData;

class Select
{

    /**
     * The HTML name attribute of the selection list.
     * @var string
     */
    private $name;

    /**
     * Whether the list should include a "blank" option.
     * @var boolean
     */
    private $blankOption = false;

    /**
     * Array of key=>value pairs of attributes for the HTML select attribute.
     * @var array
     */
    private $listAttr = [];

    /**
     * The set of values to set as selected.
     * @var array
     */
    private $selectedValues = [];


    /**
     * Constructor
     *
     * The selection list requires the name attribute to pass data. The 'id' and 'for' attributes are also assigned.
     * @param string $name The HTML name attribute of the selction list.
     * @throws Exception if $name is not string.
     */
    public function __construct($name)
    {
        if (!is_string($name)) {
            $msg = 'Expecting string, ' . gettype($name) . ' given.';
            throw new \Exception($msg, 1);
        }

        $this->name = $name;
    }

    /**
     * Formats a list of HTML option elements.
     *
     * Creates the string of formatted HTML options.
     * @param array $options The 1-dimensional array of option data.
     * @param string $value The key of the value element.
     * @param string $text They key of the text element.
     * @return mixed The formatted HTML option elements or array of elements if separator is not string.
     */
    public function selectionList($options, $valueKey, $textKey, $separator = "\n\t")
    {
        // Tests the value type
        if (!is_string($valueKey)) {
            $msg = 'Expecting string for value parameter, ' . gettype($valueKey) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Tests the text type
        if (!is_string($textKey)) {
            $msg = 'Expecting string for text parameter, ' . gettype($textKey) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Array to store formatted option strings
        $formattedArray = [];

        foreach($options as $o) {
            $formattedArray[] = $this->formatOption($o, $valueKey, $textKey);
        }

        // If using a blank option, prepend it to the start of the array
        if ($this->blankOption) {
            array_unshift($formattedArray, '<option></option>');
        }

        // By default, return a formatted string padded with newlines and tabs
        if (is_string($separator)) {

            // The HTML select element opening tag
            $s = $this->formatSelect() . $separator;

            // The formated HTML options
            $s .= implode($separator, $formattedArray);

            // Append the closing tag and return
            return $s . "\n</select>";
        }

        // If not using a string, return all elements as an array
        $a = [$this->formatSelect()];
        $a = array_merge($a, $formattedArray);
        $a[] = '</select>';

        return $a;
    }

    /**
     * Formats the opening HTML select element.
     *
     * @return string The formatted opening tag for the HTML select element.
     */
    private function formatSelect()
    {
        $sString = '<select';

        $nameAttr = ' name="' . $this->name . '"';

        // If using selection of multiple options, formats the name accordingly
        if (isset($this->listAttr['multiple'])) {
            $nameAttr = ' name="' . $this->name . '[]"';
        }

        $sString .= $nameAttr;

        // Return the formatted select element
        return $sString . $this->formatAttr($this->listAttr) . '>';
    }

    /**
     * Format an option for the selection list.
     *
     * If attributes have been set, the entry attributes array will be formatted as HTML attributes. If the attributes have not been set but values have selected then those options will still be selected.
     * @param array $option The 1-dimensional array of option data.
     * @param string $valueKey The key of the value attribute.
     * @param string $textKey They key of the text for the option.
     * @throws Exception if either $valueKey or $textKey is not a string or not a key in $options.
     */
    private function formatOption(array $option, $valueKey, $textKey)
    {
        if (!isset($option[$valueKey])) {
            $msg = 'Option missing value key for (' . $valueKey . ').';
            throw new \Exception($msg, 1);
        }
        $v = $option[$valueKey];

        if (!isset($option[$textKey])) {
            $msg = 'Option missing text key for (' . $textKey . ').';
            throw new \Exception($msg, 1);
        }
        $t = $option[$textKey];

        // Remove the reserved elements
        unset($option[$textKey], $option[$valueKey]);

        // Create an array of for the option attributes
        $o = array('value' => $v);

        // If selected values have been set
        if ($this->selectedValues) {
            if (in_array($v, $this->selectedValues, true)) {
                $o['selected'] = 'selected';
            }
        }

        return '<option'. $this->formatAttr($o) . '>' . $t . "</option>";
    }

    /**
     * Create an HTML label element for the selection list.
     *
     * @param string $text The text for the label element.
     * @param array $attributes The array of HTML attributes as key=>value pairs (attr=>value).
     * @return string The formated HTML label element.
     */
    public function label($text, $attributes = null)
    {
        // Tests the $text type
        if (!is_string($text)) {
            $msg = 'Expecting string, ' . gettype($text) . ' given.';
            throw new \Exception($msg, 1);
        }

        // The attributes array is optional, but if set must be an array of HTML attributes
        if (!is_null($attributes) && !is_array($attributes)) {
            $msg = 'Expecting array, ' . gettype($attributes) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Creates an array to store attributes
        $a = ['for' => $this->name];

        // Merges any additional attributes
        if (is_array($attributes)) {

            // If set, removes the 'for' attribute to prevent a collision
            unset($attributes['for']);

            $a = array_merge($a, $attributes);
        }

        // Formats the opening tag for the label element
        $labelString = '<label' . $this->formatAttr($a) . '>'; 

        // Appends the label text and close the tag
        return $labelString . $text . '</label>';
    }

    /**
     * Sets blankOption property to true to use  blank HTML option element.
     *
     * @return bool Returns true.
     */
    public function setBlank()
    {
        $this->blankOption = true;
        return true;
    }

    /**
     * Identifies the records to set as selected by value.
     *
     * If $selected value is boolean value false or null, it is ignored and false is returned. Any integer or string value is pushed to the selectedValues property.
     * If any array is used, the select element attrbute 'multiple' is set, even if the array is empty or contains no valid elements. This maintains consistency as a multiple selection list even if no values are selected.
     * @param mixed $match The value or values set as selected.
     * @return bool Returns true if selection options set and false if not.
     * @throws Exception if $selected is does not evaluate to false but is not string, integer, nor array.
     */
    public function setSelected($selected)
    {
        // Silentely ignores value if $selected evaluates to false
        if ($selected === false || is_null($selected)) {
            return false;
        }

        // If a scalar value is passed, pushes it to the selected array
        if (is_string($selected) || is_integer($selected)) {
            $this->selectedValues[] = $selected;
            return true;
        }

        // If $selected is not a scalar value or array, throws an exception.
        if (!is_array($selected)) {
            $msg = 'Expecting string, integer, or array for selected parameter, ' . gettype($selected) . ' given.';
            throw new \Exception($msg, 1);
        }

        // In case not passed, sets 'multiple' attribute for select element
        $a['multiple'] = 'multiple';
        $this->listAttr = array_merge($a, $this->listAttr);

        // Assigns valid values to the selectedValues property and silently ignores all others
        foreach ($selected as $v) {
            if (is_scalar($v)) {
                $this->selectedValues[] = $v;
            }
        }
        return true;
    }

    /**
     * Sets the attributes for the HTML select attribute.
     *
     * @param array $attributes Attributes for the HMTL select element as key=>value pairs.
     * @return bool Returns true.
     */
    public function listAttributes(array $attributes)
    {
        // Validates the array
        $this->validateAttr($attributes);

        // If the (reserved) name attribute has been passed, removes it
        unset($attributes['name']);

        $this->listAttr = array_merge($this->listAttr, $attributes);
        return true;
    }

    /**
     * Validates the HTML attribute array.
     *
     * The attribute array uses a key=>value pair corresponding to the attribute="value" pair in an HTML element. The attribute (key) must be a string but the value may be either a string or null.
     * @param array $attributes An associative array.
     * @return bool Returns true if all keys and values in the array pass type validation.
     * @throws Exception of any key is not a string or any value is not scalar or null.
     */
    private function validateAttr($attributes)
    {
        if (!is_array($attributes)) {
            $msg = 'Expecting array for HTML attribute key=>value pairs, ' . gettype($attributes) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Tests that each value is scalar.
        foreach ($attributes as $k => $v) {

            // Tests the the attribute (key) is a string
            if (!is_string($k)) {
                $msg = 'Expecting string for attribute key, ' . gettype($k) . ' given.';
                throw new \Exception($msg, 1);
            }

            // Tests that the value is either a string or null
            if (!is_null($v) && !is_scalar($v)) {
                $msg = "Expecting string or NULL for value of '$k' attribute, " . gettype($v) . ' given.';
                throw new \Exception($msg, 1);
            }
        }

        return true;
    }

    /**
     * Formats an array of key=>value pairs as HTML attributes.
     *
     * This method is used with the validateAttr method, which tests that the array is valid.
     * @param array $attr The key=>value pairs to format.
     * @return string A string of HTML attributes with their values (empty if none).
     */
    private function formatAttr($attr)
    {
        if (empty($attr)) {
            return '';
        }

        // Validates $attr
        $this->validateAttr($attr);

        // Attribute string
        $attrString = '';

        // Loops through the array to create attributes.
        foreach ($attr as $k => $v) {
            $attrString .= ' ' . $k . '="' . $v . '"';
        }

        return $attrString;
    }

}
