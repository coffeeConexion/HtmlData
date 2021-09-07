<?php
/**
 * Creates an HTML form.
 *
 * ***
 *
 * Created: May 22, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */

namespace CoffeeConexion\HtmlData;

class Form
{

    /**
     * The HTML form action attribute; set by constructor
     * @var string
     */
    private $action;

    /**
     * The HTML form method attribute; set by constructor.
     * @var string
     */
    private $method;


    /**
     * Form action and method are required to instantiate the object.
     *
     * The $action is usually the calling script, but any target file may be set. The method must be either 'GET' or 'POST'; the method is not case sensitive.
     * @param string $action The HTML form action (file destination).
     * @param string $method The HTTP method (either GET or POST).
     * @throws Exception if either paramter is not a string.
     */
    public function __construct($action, $method)
    {
        if (!is_string($action)) {
            $msg = 'Expecting string for action parameter, ' . gettype($action) . ' given.';
            throw new \Exception($msg, 1);
        }

        if (!is_string($method)) {
            $msg = 'Expecting string for method parameter, ' . gettype($method) . ' given.';
            throw new \Exception($msg, 1);
        }
        $this->action = $action;

        // Test that the method is either GET or POST, and assign
        if (strtoupper($method) == 'GET') {
            $this->method = 'GET';
            return;
        } 
        if (strtoupper($method) == 'POST') {
            $this->method = 'POST';
            return;
        }

        // If the method was neither GET nor POST
        $msg = 'The form method must be either GET or POST.';
        throw new \Exception($msg);
    }

    /**
     * Creates the opening HTML form tags.
     *
     * The action and method come from the properties set by the constructor but other attributes may be dynamically passed with the $attributes array.
     *
     * @param array $attributes A 1-dimesional array of key=>value pairs for HTML attribute="value" pairs.
     * @return string The HTML opening form tags.
     */
    public function openForm(array $attributes = null)
    {
        // Array of HTML attributes
        $attr = array('action'=>$this->action, 'method'=>$this->method);

        if (is_array($attributes)) {

            // If reserved attributes are set, unset them
            unset($attributes['action'], $attributes['method']);

            $attr = array_merge($attr, $attributes);

        } // End of $attributes if

        // Return the formatted element
        return '<form' . $this->formatAttr($attr) . ">";
    }

    /**
     * Returns the HTML form close tag
     *
     * @return string The HTML form closing tag.
     */
    public function closeForm()
    {
        return "</form>";
    }

    /**
     * Creates an HTML submit button element.
     *
     * @param string $value The submit button value attribute.
     * @param array $attributes A 1-dimesional array of key=>value pairs for HTML attribute="value" pairs.
     * @return string A formatted HTML submit element.
     * @throws Exception if $value is not a string.
     */
    public function submitButton($value, array $attributes = null)
    {
        // Test the $value type
        if (!is_string($value)) {
            $msg = 'Expecting string for value parameter, ' . gettype($value) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Array of HTML attributes
        $attr = array('type'=>'submit', 'value'=>(string) $value);

        if (is_array($attributes)) {
            //$this->validateAttr($attributes);

            // If the reseved attributes are set, remove them
            unset($attributes['type'], $attributes['value']);

            // Merge $attributes with the other attributes
            $attr = array_merge($attr, $attributes);
        }

        // Return the formatted HTML input element
        return '<input' . $this->formatAttr($attr) . ' />';
    }

    /**
     * Creates an HTML reset button.
     *
     * @param string $value The reset button value attribute.
     * @param array $attributes A 1-dimesional array of key=>value pairs for HTML attribute="value" pairs.
     * @return string A formatted HTML reset element.
     * @throws Exception if $value is not a string.
     */
    public function resetButton($value, array $attributes = null)
    {
        // Test the $value type
        if (!is_string($value)) {
            $msg = 'Expecting string for value parameter, ' . gettype($value) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Array of HTML attributes
        $attr = array('type'=>'reset', 'value'=>(string) $value);

        if (is_array($attributes)) {

            // If the reseved attributes are set, remove them
            unset($attributes['type'], $attributes['value']);

            // Merge $attributes with the other attributes
            $attr = array_merge($attr, $attributes);
        }

        // Return the formatted HTML input element
        return '<input' . $this->formatAttr($attr) . ' />';
    }

    /**
     * Create an HTML hidden input element.
     *
     * @param string $name The HTML name attribute of this element.
     * @param string $value The HTML value attribute of this element.
     * @param array $attributes An array of key=>value pairs for HTML attributes
     * @throws Exception if name is not a string or if value is neithera string nor an integer.
     */
    public function hiddenInput($name, $value, $attributes = null)
    {
        if (!is_string($name)) {
            throw new \Exception('The hidden field name must be string.', 1);
        }

        if (!is_scalar($value)) {
            throw new \Exception('The hidden field value must be scalar.', 1);
        }

        // Cast as string (in case numeric passed)
        $value = (string) $value;

        // Set the array of HTML attributes
        $attr = array('type'=>'hidden', 'name'=>$name, 'value'=>$value);

        // If an array of attributes has been passed, merge it into $attr
        if (is_array($attributes)) {

            // Unset any reserved attributes
            unset($attributes['type'], $attributes['name'], $attributes['value']);

            $attr = array_merge($attr, $attributes);
        }

        // Return the formatted HTML input element
        return '<input' . $this->formatAttr($attr) . ' />';
    }

    /**
     * Create an HTML opening fieldset tag.
     *
     * @param array $attributes A 1-dimesional array of key=>value pairs for HTML attribute="value" pairs.
     * @return string The formatted HTML fieldset element.
     */
    public function openFieldset(array $attributes = null)
    {
        // HTML attribute string
        $attrString = '';

        // If an array of attributes is passed
        if (is_array($attributes)) {
            $attrString = $this->formatAttr($attributes);
        }

        // CLose the fieldset opening tag
        return '<fieldset' . $attrString . ">";

        return $fieldsetString;
    }

    /**
     * Create the closing HTML fieldset tag.
     *
     * @return string The closing tag for the HTML fieldset element.
     */
    public function closeFieldset()
    {
        return "</fieldset>";
    }

    /**
     * Create an HTML legend element.
     *
     * @param string $text The legend text to display.
     * @param array $attributes A 1-dimesional array of key=>value pairs for HTML attribute="value" pairs.
     * @return string The formatted HTML legend element.
     * @throws Exception if $text is not string.
     */
    public function legend($text, array $attributes = null)
    {
        if (!is_string($text)) {
            $msg = 'Expecting string for legend text, ' . gettype($text) . ' given.';
            throw new \Exception($msg, 1);
        }

        // HTML attribute string
        $attrString = '';

        if (is_array($attributes)) {
            $attrString = $this->formatAttr($attributes);
        } // End of $attributes if

        // Format the opening HTML legend tag
        $legendString = '<legend' . $attrString . '>';

        // Return the string with appended text and closing HTML legend tag
        return $legendString . $text . "</legend>";
    }

    /**
     * Validate the HTML attribute array.
     *
     * The attribute array uses a key=>value pair corresponding to the attribute="value" pair in an HTML element. The attribute (key) must be a string but the value may be either a string or null.
     * @param array $attributes An associative array of string element with string keys.
     * @return boolean TRUE if all keys and values in the array pass type validation.
     * @throws Exception of any key is not a string or any value is not scalar or null.
     */
    private function validateAttr(array $attributes)
    {
        if (!is_array($attributes)) {
            $msg = 'Expecting array for HTML attribute key=>value pairs, ' . gettype($attributes) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Test that each value is scalar.
        foreach ($attributes as $k => $v) {

            // Test the the attribute (key) is a string
            if (!is_string($k)) {
                $msg = "Expecting string for attribute key $k, " . gettype($k) . ' given.';
                throw new \Exception($msg, 1);
            }

            // Test that the value is either a string or null
            if (!is_string($v) && !is_null($v)) {
                $msg = "Expecting string or NULL for value of '$k' attribute, " . gettype($v) . ' given.';
                throw new \Exception($msg, 1);
            }
        }

        return true;
    }

    /**
     * Formats an array of key=>value pairs as HTML attributes.
     *
     * This method is used with the validateAttr() method which tests that the array is valid.
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
