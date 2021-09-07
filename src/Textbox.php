<?php
/**
 * Creates an HTML textbox or textarea.
 *
 * Textbox (input) and textarea elements are nearly identical in terms 
 * of construction. Orininally these were conceived as separate classes 
 * as they are distinct HTML elements, but the design ended up being the 
 * same apart from the actual HTML tags. Since the textbox (input) element 
 * is in a sense more fundamental, it is the default type. The textarea 
 * may be used by passing a third optional parameter (true) or by using the 
 * alias method 'textarea', which calls 'textbox' and sets that third 
 * parameter.
 *
 * ***
 *
 * Created: May 22, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */

namespace CoffeeConexion\HtmlData;

class Textbox
{

    /**
     * The textbox 'name' attribute (required, set at constructor).
     * @var string
     */
    private $name;


    /**
     * Sets the HTML name attribute.
     *
     * @param string $name The HTML 'name' attribute.
     * @throws Exception if $name is not a string.
     */
    public function __construct($name) {

        // Tests the data type.
        if (!is_string($name)) {
            $msg = 'Expecting string, ' . gettype($name) . ' given.';
            throw new \Exception($msg, 1);
        }

        $this->name = $name;
    }

    /**
     * Creates an HTML textbox.
     *
     * Creates a formatted HTML textbox element. For ease of use, the $primary attribute may be either a scalar (or NULL) value for the HTML value attribute, or an array of attributes (which also may contain a value attribute). The second parameter is an array of key=>value pairs for HTML attributes, to be used if a scalar or null value is passed to the first parameter.
     * @param mixed $primary Either a string for the value or an array of attributes.
     * @param array $attributes A array of key=>value pairs for HTML attributes.
     * @param bool True = textarea element; false = textbox element.
     * @return string The HTML element.
     */
    public function textbox($primary = null, array $attributes = null, $textarea = false)
    {
        // Textarea case
        if ($textarea == true) {
            return $this->formatTextarea($primary, $attributes);
        }

        // Set an initial array with the attributes of name
        $attr = ['type' => 'text', 'name' => $this->name];

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
     * Creates an HTML textarea.
     *
     * Alias of textbox with textarea parameter set.
     * @param mixed $primary Either a string for the value or an array of attributes.
     * @param array $attributes A array of key=>value pairs for HTML attributes.
     * @return string The HTML textarea.
     */
    public function textarea($primary = null, array $attributes = null)
    {
        return $this->textbox($primary, $attributes, true);
    }

    /**
     * Returns formatted textarea element for textbox method.
     *
     * @param text 
     * @return string Formatted HTML textarea element.
     * */
    private function formatTextarea($text = null, $attributes = null)
    {
        // Tests the type
        if (!is_null($text) && !is_scalar($text)) {
            $msg = 'Expecting null or scalar for text paramter, ' . gettype($text) . ' given.';
            throw new \Exception($msg, 1);
        }

        if (is_null($text)) {
            $text = '';
        }

        // Casts to string (in case numeric)
        $text = (string) $text;

        // Array for attributes
        $a = ['name' => $this->name];

        if (is_array($attributes)) {
            $a = array_merge($a, $attributes);
        }

        return '<textarea' . $this->formatAttr($a) . '>' . $text . '</textarea>';
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
