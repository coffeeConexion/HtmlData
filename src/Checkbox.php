<?php
/**
 * Creates an HTML checkbox or set of checkboxes with same name (checklist).
 *
 * ***
 *
 * Created: May 22, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */

namespace CoffeeConexion\HtmlData;

class Checkbox
{

    /**
     * The name of the selection list.
     * @var string
     */
    private $name;

    /**
     * TRUE indicates a checklist is in use for multiple values under the same name (use attribute="value[]").
     * @var boolean
     */
    private $useList = false;

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
     * The checkbox to mark checked (by text).
     * @var array
     */
    private $checkedOptions = [];

    /**
     * Reserved attributes that may not be set by the user.
     * @var array
     */
    private $reservedAttr = ['type', 'name', 'value'];


    /**
     * Sets the checklist 'name' attribute.
     *
     * @param string $name The checklist 'name' attribute.
     * @throws Exception if $name is not a string.
     */
    public function __construct($name)
    {
        if (!is_string($name)) {
            $msg = 'Expecting string for HTML name attribute, ' . gettype($name) . ' given.';
            throw new \Exception($msg, 1);
        }

        $this->name = $name;
    }

    /**
     * Creates a single HTML checkbox.
     *
     * This method is the foundation of the class. The checklist method applies this method to an array of checklist options, with additional functionality to manage attributes, including whether elements are "checked". As the state (checked/unchecked) is the most important attribute, the third parameter does double duty for either:
     * * setting the state (checked = anything non-empty and non-array; default is not checked)
     * * passing an array of attributes (which may or may not contain the "checked" attribute)
     * The intention is to strike a balance between an easy parameter to set on the fly while using the same functionality for the checklist method.
     *
     * @param mixed $value The HTML value attribute.
     * @param mixed $text The test to display.
     * @param mixed $inputAttr Either a boolean flag to indicate that the box is checked or an array of HTML attribute=>value pairs; optional.
     * @param mixed $labelAttr An array of key=>value pairs for the label attribute.
     * @throws Exception if any parameter is invalid.
     * @return string A HTML checkbox.
     */
    public function checkbox($value, $text, $inputAttr = null, $labelAttr = null)
    {
        if (!is_scalar($value)) {
            $msg = 'Expecting scalar for value parameter, ' . gettype($value) . ' given.';
            throw new \Exception($msg, 1);
        }

        if (!is_scalar($text)) {
            $msg = 'Expecting scalar for text parameter, ' . gettype($text) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Starts the HTML string with the label element
        $checkboxString = $this->formatLabel($labelAttr);

        // Creates the attribute array and uses the $inputAttr parameter (if set)
        $a = [];

        // If the $inputAttr parameter has been set and not empty
        if (!empty($inputAttr)) {

            // If any array of checkbox elements attributes
            if (is_array($inputAttr)) {
                $a = $inputAttr;

                // Removes any reserved attributes to prevent collisions
                foreach ($this->reservedAttr as $r) {
                    unset($a[$r]);
                }
            } else { // if any other non-empty value, set the 'checked' attribute
                $a['checked'] = 'checked';
            }
        }

        // Format and append the HTML for the input element
        $checkboxString .= $this->formatInput($value, $a);

        // Append the text and closing label tag
        $checkboxString .= $text . "</label>";

        return $checkboxString;
    }

    /**
     * Creates a set of HTML checkboxes with the same name.
     *
     * The helper methods setLabelAttr and setInputAttr set HTML attributes for each checkbox for the label and input (checkbox) elements respectively. The helper method setChecked sets the checked attribute for any input element with the a matching value; value is used rather than text to fascilate catching the data submitted through the form directly rather than the additional step of matching values to their text.
     * @param array $options The 2-dimesional array of option data.
     * @param string $value The key for the HTML value attribute in the $options array.
     * @param string $text The key for the label in the $options array.
     * @param string $separator The string to pad between formatted options (glue for implode).
     * @return mixed The HTML checklist (set of checkboxes) as either an array or a string.
     * @throws Exception if any argument is invalid.
     */
    public function checklist(array $options, $valueKey, $textKey, $separator = '')
    {
        // Validates the $options array
        if (!is_array($options)) {
            $msg = 'Expecting array, ' . gettype($options) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Sets the checklist flag
        $this->useList = true;

        $formattedOptions = [];

        foreach ($options as $row => $option) {

            $this->validateAttr($option);

            if (!isset($option[$valueKey])) {
                $msg = "Could not find key '" . $valueKey . "' for option '" . $row . "' in the options array.";
                throw new \Exception($msg, 1);
            }

            if (!isset($option[$textKey])) {
                $msg = "Could not find key '" . $textKey . "' for option '" . $row . "' in the options array.";
                throw new \Exception($msg, 1);
            }

            // Sets the local variables and remove these from the array
            $v = $option[$valueKey];
            $t = $option[$textKey];
            unset($option[$valueKey], $option[$textKey]);

            // Pushes the formatted HTML for the option to the array
            $formattedOptions[] = $this->checkbox($v, $t, $option);
        }

        // If returning the formatted options as a string
        if (is_string($separator)) {
            return implode($separator, $formattedOptions);
        }

        // Return the array of options
        return $formattedOptions;
    }

    /**
     * Sets the array of label element attributes.
     */
    public function setLabelAttr(array $array)
    {
        // Validates the attribute $array
        $this->validateAttr($array);

        // Sets this array as the input attribute array
        $this->labelAttr = $array;
    }

    /**
     * Sets the array of input element attributes.
     *
     * @param $array The array of name=>value pairs to set as HTML attributes and values.
     */
    public function setInputAttr(array $array)
    {
        $a = $array;

        // Validates the array
        $this->validateAttr($a);

        // Removes any reserved keywords to prevent collisions
        foreach ($a as $k => $v) {
            if (in_array($k, $this->reservedAttr, true)) {
                unset($a[$k]);
            }
        }

        // Sets this array as the input attribute array
        $this->inputAttr = $a;
    }

    /**
     * Sets the array of checked options with reference to the checkbox element's value attribute.
     *
     * Validates the array for scalar values and sets the checkedOptions property. The $checkedArray is based on the HTML value attribute. This makes it easy to take data submitted from a form to populate/update a checklist using an array from the form data.
     * @param array $checkedArray The array options to set as checked.
     * @throws Exception if any element is not a string.
     */
    public function setChecked(array $checkedArray)
    {
        // Loops through the array to validate all options
        foreach ($checkedArray as $k => $v) {
            if (!is_string($v) && !is_int($v)) {
                $msg = "Expecting string or integer for checkedOptions key, for key '$k'" . gettype($v) . ' given.';
                throw new \Exception($msg, 1);
            }
        }

        // Sets this array as the input attribute array
        $this->checkedOptions = $checkedArray;
    }

    /**
     * Validates the HTML attribute array.
     *
     * The attribute array uses a key=>value pair corresponding to the attribute="value" pair in an HTML element. The attribute (key) must be a string but the value may be either a string or NULL.
     * @param array $attributes An associative array.
     * @return boolean True if all keys and values in the array pass type validation.
     * @throws Exception of any key is not a string or any value is not scalar or NULL.
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
                $msg = "Expecting string for attribute key $k, " . gettype($k) . ' given.';
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
    private function formatAttr(array $attr)
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

    /**
     * Formats the label element, including attributes.
     *
     * @param mixed $lAttr Optional array of HTML label attributes as key=>value pairs, silently ignore if non-array passed.
     * @return string The opening tag for the HTML label attibute.
     */
    private function formatLabel($lAttr = null)
    {
        // Uses the object property as the array base (empty unless set with valid array)
        $a = $this->labelAttr;

        // Merges $lAttr, overwriting with the labelAttr property in case of any conflicts
        if (is_array($lAttr)) {
            $a = array_merge($lAttr, $a);
        }

        // Format the label element array (includes array validation)
        return '<label' . $this->formatAttr($a) . '>';
    }

    /**
     * Formats the input element, including attributes.
     *
     * @param string $value The HTML value attribute.
     * @param array $iAttr Optional array of HTML input attribute as key=>value pairs; silently ignored if non-array passed.
     * @return string The HTML input element.
     */
    private function formatInput($value, $iAttr)
    {
        // Attribute array
        $a = ['type' => 'checkbox',
            'name' => $this->name,
            'value' => $value
        ];

        // If using checklist method, appends '[]' to the attribute name (for multiple values for same name)
        if ($this->useList) {
            $a['name'] = $this->name .'[]';
        }

        // Merges any attributes passed as a parameter with the required input attributes
        $a = array_merge($a, $this->inputAttr, $iAttr);

        // Any option with a value in the checkedOptions propertt is set to checked
        if (!empty($this->checkedOptions)) {
            if (in_array($value, $this->checkedOptions, true)) {
                $a['checked'] = 'checked';
            }
        }

        // Closes the input element and return the string
        return '<input' . $this->formatAttr($a) . ' />';
    }

}
