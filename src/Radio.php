<?php
/**
 * This class creates an HTML radio input (radio button).
 *
 * ***
 *
 * Created: May 22, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */

namespace CoffeeConexion\HtmlData;

class Radio
{

    /**
     * The name attribute for all options.
     * @var string
     */
    private $name;

    /**
     * The checked option (if set).
     * @var string
     */
    private $checked = null;

    /**
     * The array of HTML attributes for the label element (all options).
     * @var array
     */
    private $labelAttr = [];

    /**
     * The array of HTML attributes for the input element (all options).
     * @var array
     */
    private $inputAttr = [];

    /**
     * Reserved attributes that may not be set by the user. (Checked must 
     * be set by setChecked method, not as a passed attribute.)
     * @var array
     */
    private $reservedAttr = ['type', 'name', 'value', 'checked'];


    /**
     * The HTML name attribute is shared by all radio button options (of 
     * the same object).
     *
     * @param string $name The HTML name attribute.
     * @throws Exception if $name is not a string.
     */
    public function __construct($name)
    {
        // Tests whether the name element is a string
        if (!is_string($name)) {
            $msg = 'Expecting string for the HTML name attribute, ' . gettype($name) . ' given.';
            throw new \Exception($msg, 1);
        }

        $this->name = $name;
    }

    /**
     * Creates all the HTML radio buttons.
     *
     * This method tests for a keyword index, and if not set looks for a numeric index to assign the text (required), the HTML value (required), and the HTML id (optional). The keywords match the paramter names ('text', 'value', and 'id').
     *
     * @param array $optionArray The 2-dimensional array of radio button options.
     * @param string $value The key for the value text.
     * @param string $text The key for the label text.
     * @param string $separator The string between options (defaults to empty string).
     * @throws Exception if $optionArray is not a 2-dimensional array, or if the 'text' or 'value' keys do no exist.
     */
    public function radio($optionArray, $value, $text, $separator = '')
    {
        if (!is_string($value)) {
            $msg = 'Expecting string for value parameter, ' . gettype($value) . ' given.';
            throw new \Exception($msg, 1);
        }
        if (!is_string($text)) {
            $msg = 'Expecting string for text parameter, ' . gettype($text) . ' given.';
            throw new \Exception($msg, 1);
        }

        if (!is_array($optionArray)) {
            $msg = 'Option array must be a 2-dimensional array, containing arrays with the text and HTML value attribute.';
            throw new \Exception($msg, 1);
        }

        // Array to store formatted options (HTML label and input elements)
        $formattedOptions = [];

        // Loop through the $optionArray to test that each is an array, then check for keywords/numeric index
        foreach ($optionArray as $k => $v) {

            // Tests that it is an array
            if (!is_array($v)) {
                $msg = 'Option array must be a 2-dimensional array, containing arrays with the text and HTML value attribute.';
                throw new \Exception($msg, 1);
            }

            // Tests whether $text key is set
            if (!isset($v[$text])) {
                $msg = 'Key ' . $text . ' was not set for option ' . $k . '.';
                throw new \Exception($msg, 1);
            }

            // Tests whether $value key is set
            if (!isset($v[$value])) {
                $msg = 'Key ' . $value . ' was not set for option ' . $k . '.';
                throw new \Exception($msg, 1);
            }

            // Assigns the values as will be passed to the formatOption method
            $vActual = $v[$value];
            $tActual = $v[$text];

            // Unsets the text and value keys so remainder of attributes may be passed for formatting
            unset($optionArray[$k][$text], $optionArray[$k][$value]);

            // Appends the HTML for this radio button to the $radioString
           $formattedOptions[] = $this->formatOption($vActual, $tActual, $optionArray[$k]);
        }

        return implode($separator, $formattedOptions);
    }

    /**
     * Formats HTML label and input elements for an option.
     *
     * Creates an HTML radio input (radio button) inside of a label element.
     *
     * @param string $value The HTML value attribute
     * @param string $text The text label of the radio button option
     * @param array $labelAttr Array of HTML label attributes as key=>value pairs.
     * @return string The HTML of the radio button option.
     */
    private function formatOption($value, $text, $labelAttr = null)
    {
        // Tests the value parameter
        if (!is_string($value) || $value == '') {
            $msg = 'Expecting string for value attribute, ' . gettype($value) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Tests the text parameter
        if (!is_string($text) || $text == '') {
            $msg = 'Expecting string for text attribute, ' . gettype($text) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Creates the array of label attributes for all options
        $l = $this->labelAttr;

        // If label attributes passed from options array
        if (is_array($labelAttr) && !empty($labelAttr)) {

            $this->validateAttr($labelAttr);

            // Merges the array of attributes passed for this label with attributes for all options
            $l = array_merge($l, $labelAttr);
        }

        // Formats the HTML string to return, starting with the label element
        $optionString = '<label' . $this->formatAttr($l) . '>';

        // Input element attribute array
        $inputAttributes = array(
            'type' => 'radio',
            'name' => $this->name,
            'value' => $value
        );

        // If input attributes for all options set, merges those into $inputAttributes
        if (!empty($this->inputAttr)) {
            $inputAttributes = array_merge($inputAttributes, $this->inputAttr);
        }

        // If this option is checked
        if ($this->checked == $value) {
            $inputAttributes['checked'] = 'checked';
        }

        // Appends the input element, with formatted attributes
        $optionString .= '<input' . $this->formatAttr($inputAttributes) . ' />';

        // Appends the text, and close the HTML label element
        $optionString .= $text . '</label>';

        return $optionString;
    }

    /**
     * Sets a value as checked.
     *
     * @param string $optionValue The value for the option to mark as checked.
     * @throws Exception if parameter is not string.
     */
    public function setChecked($optionValue)
    {
        if (!is_string($optionValue)) {
            $msg = 'Expecting string for checked option value, ' . gettype($optionValue) . ' given.';
            throw new \Exception($msg, 1);
        }

        $this->checked = $optionValue;
    }

    /**
     * Sets the array of label element attributes.
     */
    public function setLabelAttr(array $labelArray)
    {
        // Validate the attribute $array
        $this->validateAttr($labelArray);

        // Set this array as the input attribute array
        $this->labelAttr = $labelArray;
    }

    /**
     * Sets the array of input element attributes.
     */
    public function setInputAttr(array $inputArray)
    {
        // Validate the attributes
        $this->validateAttr($inputArray);

        // Remove any reserved attributes
        foreach ($inputArray as $k => $v) {

            // If the key is the name of a reserved attribute, removes it
            if (in_array($k, $this->reservedAttr, true)) {
                unset($inputArray[$k]);
            }
        }

        // Set this array as inputAttr
        $this->inputAttr = $inputArray;
    }

    /**
     * Validates the HTML attribute array.
     *
     * The attribute array uses a key=>value pair corresponding to the attribute="value" pair in an HTML element. The attribute (key) must be a string but the value may be either a string or null.
     * @param array $attributes An associative array.
     * @return boolean True if all keys and values in the array pass type validation.
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
     * @return string A string of HTML attributes with their values.
     */
    private function formatAttr(array $attr)
    {
        // Validate $attr
        $this->validateAttr($attr);

        // Attribute string
        $attrString = '';

        // Loop through the array to create attributes.
        foreach ($attr as $k => $v) {
            $attrString .= ' ' . $k . '="' . $v . '"';
        }

        return $attrString;
    }

}
