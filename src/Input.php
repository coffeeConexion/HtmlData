<?php
/**
 * Creates an HTML input element.
 *
 * The Textbox, Checkbox and RadioButton elements each have their own class.
 * This is intended for any other valid type of HTML input element.
 *
 * Source: @see https://www.w3.org/Submission/web-forms2/
 *
 * ***
 *
 * Created: August 19, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */

namespace CoffeeConexion\HtmlData;

class Input
{

    /**
     * The input 'name' attribute (required, set at constructor).
     * @var string
     */
    private $name;

    /**
     * The input 'type' attribute (required, set at constructor).
     * @var string
     */
    private $inputType;

    /**
     * Reserved attributes that may not be set by the user.
     * @var array
     */
    private $reservedAttr = ['type', 'name'];

    /**
     * Set of valid input types.
     * @var array
     */
    private $validTypes = [
        'checkbox',
        'color',
        'date',
        'datetime-local',
        'email',
        'file',
        'image',
        'hidden',
        'month',
        'number',
        'password',
        'radio',
        'range',
        'reset',
        'submit',
        'tel',
        'text',
        'time',
        'url',
        'week'
    ];


    /**
     * Sets the HTML name attribute.
     *
     * @param string $name The HTML 'name' attribute.
     * @throws Exception if $name is not a string.
     */
    public function __construct($name, $inputType)
    {
        // Tests the $name type
        if (!is_string($name)) {
            $msg = 'Expecting string for name paramter, ' . gettype($name) . ' given.';
            throw new \Exception($msg, 1);
        }

        // Tests the $inputType type
        if (!is_string($inputType)) {
            $msg = 'Expecting string for type parameter, ' . gettype($inputType) . ' given.';
            throw new \Exception($msg, 1);
        }

        $this->name = $name;
        $this->inputType = $inputType;
    }

    /**
     * Creates an input element.
     *
     * Creates a formatted HTML input element.
     * @param array $inputAttr A array of key=>value pairs for HTML attributes.
     * @return string The HTML element.
     */
    public function input_old(array $inputAttr = null)
    {
        // Creates the attribute array
        $a = ['type' => $this->inputType, 'name' => $this->name];

        if (!empty($inputAttr)) {

            // If any array of checkbox elements attributes
            if (is_array($inputAttr)) {
                $a = $inputAttr;

                // Removes any reserved attributes to prevent collisions
                foreach ($this->reservedAttr as $r) {
                    unset($a[$r]);
                }
                $a = array_merge($a, $inputAttr);
            }
        }

        // Returns the formatted HTML element
        return '<input' . $this->formatAttr($inputAttr) . ' />';
    }

    /**
     * Creates an HTML input element.
     *
     * Creates a formatted HTML input element. For ease of use, the $primary attribute may be either a scalar (or NULL) value for the HTML value attribute, or an array of attributes (which also may contain a value attribute). The second parameter is an array of key=>value pairs for HTML attributes, to be used if a scalar or null value is passed to the first parameter.
     * @param mixed $primary Either a string for the value or an array of attributes.
     * @param array $attributes A array of key=>value pairs for HTML attributes.
     * @return string The HTML element.
     */
    public function input($primary = null, array $attributes = null)
    {
        // Set an initial array with the attributes of name
        $attr = ['type' => $this->inputType, 'name' => $this->name];
        
        // If the $primary parameter is intended as the (scalar) HTML 'value' attribute
        if (is_scalar($primary)) {
            $attr['value'] = $primary;

            // If the $attributes array is set, removes the value to prevent data collisions
            if (is_array($attributes)) {
                unset($attributes['value']);
            }
        }

        // If the $primary parameter is arrray of attributes
        if (is_array($primary)) {

            // In case set, removes the reserved attributes
            unset($primary['type'], $primary['name']);

            // Merge these attributes into the $attr array
            $attr = array_merge($attr, $primary);
        }

        // If an array of HTML attributes has been passed, merges it into $attr (or silently ignore)
        if (is_array($attributes)) {

            // In case set, removes the reserved attributes
            unset($attributes['type'], $attributes['name']);

            // Merges these attributes into the $attr array
            $attr = array_merge($attr, $attributes);
        }

        // Returns the formatted HTML element
        return '<input' . $this->formatAttr($attr) . ' />';
    }

    /**
     * Creates an HTML label element.
     *
     * @param string $label The text for the label.
     * @param array $attributes The optional HTML attributes as key=>value pairs.
     * @return string The HTML label element.
     * @throws Exception if $label is not a string.
     */
    public function label($label, array $attributes = null)
    {
        // Tests the $label type
        if (!is_string($label)) {
            $msg = 'Expecting string, ' . gettype($label) . ' given.';
            throw new \Exception($msg, 1);
        }

        $attr = ['for' => $this->name];

        // If the reserved 'for' attribute has been set, removes it to prevent a collision
        unset($attributes['for']);

        // If the $attributes parameter has been set, merges into the local $attr array
        if (is_array($attributes)) {
            $attr = array_merge($attr, $attributes);
        }

        // Creates the opening label tag
        $labelString = '<label' . $this->formatAttr($attr) . '>';

        // Appends the text and the closing label tag and returns the formatted HTML
        return $labelString . $label . '</label>';
    }

    /**
     * Validate the HTML attribute array.
     *
     * The attribute array uses a key=>value pair corresponding to the attribute="value" pair in an HTML element. The attribute (key) must be a string but the value may be either a string or null.
     * @param array $attributes An associative array.
     * @return boolean TRUE if all keys and values in the array pass type validation.
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
