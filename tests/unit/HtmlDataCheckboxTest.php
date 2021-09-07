<?php

use PHPUnit\Framework\TestCase;

/**
 * Test suite for CoffeeConexion\HtmlData\Checkbox.
 *
 * ***
 *
 * Created: August 9, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */
class HtmlDataCheckboxTest extends TestCase
{

    /**
     * The default checkbox object.
     * @var object
     */
    protected $cb;

    /**
     * The checkbox name attribute.
     * @var string
     */
    protected $name = 'some_name';

    /**
     * Standard checkbox value.
     * @var string
     */
    protected $value = 'some_value';

    /**
     * Standard checkbox text.
     * @var string
     */
    protected $text = 'some_text';

    /**
     * Standard checklist value key.
     * @var string
     */
    protected $valueKey = 'value_key';

    /**
     * Standard checklist text key.
     * @var string
     */
    protected $textKey = 'text_key';

    /**
     * The default checklist options array.
     * @var string
     */
    protected $checklistOptions = [
            0 => [
                'value_key' => 'value1',
                'text_key' => 'text1'
            ],
            1 => [
                'value_key' => 'value2',
                'text_key' => 'text2'
            ],
            2 => [
                'value_key' => 'value3',
                'text_key' => 'text3'
            ]
        ];

    public function setUp(): void
    {
        $this->cb = new CoffeeConexion\HtmlData\Checkbox($this->name);
    }

    /**
     * Trims whitespace from all lines in a string.
     *
     * Both trims whitespace and removed empty lines for a clean text comparison of lines.
     * @param $string String with leading or trailing whitepace on lines.
     * @return $string Trimmed string.
     */
    private function trimWhitespace($string): string
    {
        $lines = explode("\n", $string);
        $a = [];

        foreach ($lines as $key => $value) {
            $value = trim($value);

            // Push non-empty lines into the array
            if (!empty($value)) {
                $a[] = $value;
            }
        }

        // Return the cleaned string
        return implode("\n", $a);
    }

    /** @test */
    public function That_a_checkbox_object_can_be_created(): void
    {
        $o = new CoffeeConexion\HtmlData\Checkbox($this->name);
        $this->assertIsObject($o);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_name(): void
    {
        // Name array test
        $badName = [];

        $expectedMsg = 'Expecting string for HTML name attribute, array given.';
        $actualMsg = '';

        try {
            $o = new CoffeeConexion\HtmlData\Checkbox($badName);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Name integer test
        $badName = 1;
        $expectedMsg = 'Expecting string for HTML name attribute, integer given.';
        $actualMsg = '';

        try {
            $o = new CoffeeConexion\HtmlData\Checkbox($badName);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_checkbox_method_creates_html_checkbox_element(): void
    {
        $expectedHtml = '<label><input type="checkbox" name="some_name" value="some_value" />some_text</label>';
        $actualHtml = $this->cb->checkbox($this->value, $this->text);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_checkbox_input_parameter_accepts_scalar_for_checked(): void
    {
        $inputAttr = 1;
        $expectedHtml = '<label><input type="checkbox" name="some_name" value="some_value" checked="checked" />some_text</label>';
        $actualHtml = $this->cb->checkbox($this->value, $this->text, $inputAttr);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_checkbox_input_parameter_accepts_attribute_array(): void
    {
        $inputAttr = ['input_attr' => 'input_value'];
        $expectedHtml = '<label><input type="checkbox" name="some_name" value="some_value" input_attr="input_value" />some_text</label>';
        $actualHtml = $this->cb->checkbox($this->value, $this->text, $inputAttr);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_checkbox_label_parameter_accepts_attribute_array(): void
    {
        $labelAttr = ['label_attr' => 'label_value'];
        $expectedHtml = '<label label_attr="label_value"><input type="checkbox" name="some_name" value="some_value" />some_text</label>';
        $actualHtml = $this->cb->checkbox($this->value, $this->text, null, $labelAttr);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_value(): void
    {
        // Value array test
        $badValue = [];

        $expectedMsg = 'Expecting scalar for value parameter, array given.';
        $actualMsg = '';

        try {
            $this->cb->checkbox($badValue, $this->text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Value null test
        $badValue = null;
        $expectedMsg = 'Expecting scalar for value parameter, NULL given.';
        $actualMsg = '';

        try {
            $this->cb->checkbox($badValue, $this->text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_text(): void
    {
        // Text array test
        $badText = [];

        $expectedMsg = 'Expecting scalar for text parameter, array given.';
        $actualMsg = '';

        try {
            $this->cb->checkbox($this->value, $badText);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Text null test
        $badText = null;
        $expectedMsg = 'Expecting scalar for text parameter, NULL given.';
        $actualMsg = '';

        try {
            $this->cb->checkbox($this->value, $badText);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_attribute_arrays_are_validated(): void
    {
        // Integer key test
        $badAttribute = [1 => 'some_value']; // Nested array
        $expectedMsg = "Expecting string for attribute key 0, integer given.";
        $actualMsg = '';

        try {
            $s = $this->cb->checkbox($this->value, $this->text, $badAttribute);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Array for value test
        $badAttribute = ['a' => ['a']]; // Nested array
        $expectedMsg = "Expecting string or NULL for value of 'a' attribute, array given.";
        $actualMsg = '';

        try {
            $s = $this->cb->checkbox($this->value, $this->text, $badAttribute);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_checkbox_method_ignores_reserved_input_attributes(): void
    {
        $attr = ['type' => 'some_other_type', 'value' => 'other_value'];

        $expectedHtml = '<label><input type="checkbox" name="' . $this->name .'" value="some_value" />some_text</label>';
        $actualHtml = $this->cb->checkbox($this->value, $this->text, $attr);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_checklist_method_creates_a_set_of_html_checkboxes(): void
    {
        $expectedHtml = '<label><input type="checkbox" name="' . $this->name .'[]" value="value1" />text1</label>'
            . '<label><input type="checkbox" name="' . $this->name .'[]" value="value2" />text2</label>'
            . '<label><input type="checkbox" name="' . $this->name .'[]" value="value3" />text3</label>';
        $actualHtml = $this->cb->checklist($this->checklistOptions, $this->valueKey, $this->textKey, '');

        // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_checklist_method_throws_exception_for_missing_value_key(): void
    {
        $otherValueKey = 'other_value_key';

        // Array for value test
        $expectedMsg = "Could not find key '" . $otherValueKey . "' for option '0' in the options array.";
        $actualMsg = '';

        try {
            $s = $this->cb->checklist($this->checklistOptions, $otherValueKey, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_checklist_method_throws_exception_for_missing_text_key(): void
    {
        $otherTextKey = 'other_text_key';

        // Array for value test
        $expectedMsg = "Could not find key '" . $otherTextKey . "' for option '0' in the options array.";
        $actualMsg = '';

        try {
            $s = $this->cb->checklist($this->checklistOptions, $this->valueKey, $otherTextKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setChecked_sets_checked_elements(): void
    {
        $name = 'some_name';

        // New object
        $cList = new CoffeeConexion\HtmlData\Checkbox($name);

        $checkedArray = ['value1', 'value3'];
        $cList->setChecked($checkedArray);

        $valueKey = 'value_key';
        $textKey = 'text_key';
        $options = $this->checklistOptions;

        $expectedHtml = '<label><input type="checkbox" name="' . $name .'[]" value="value1" checked="checked" />text1</label>'
            . '<label><input type="checkbox" name="' . $name .'[]" value="value2" />text2</label>'
            . '<label><input type="checkbox" name="' . $name .'[]" value="value3" checked="checked" />text3</label>';
        $actualHtml = $cList->checklist($options, $valueKey, $textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setLabelAttr_sets_attributes_for_label_elements(): void
    {
        $cList = new CoffeeConexion\HtmlData\Checkbox($this->name);

        $labelAttr = ['attr1'=>'value1', 'attr2'=>'value2'];
        $cList->setLabelAttr($labelAttr);


        $expectedHtml = '<label attr1="value1" attr2="value2"><input type="checkbox" name="' . $this->name .'[]" value="value1" />text1</label>'
            . '<label attr1="value1" attr2="value2"><input type="checkbox" name="' . $this->name .'[]" value="value2" />text2</label>'
            . '<label attr1="value1" attr2="value2"><input type="checkbox" name="' . $this->name .'[]" value="value3" />text3</label>';
        $actualHtml = $cList->checklist($this->checklistOptions, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setInputAttr_sets_attributes_for_input_elements(): void
    {
        // New object
        $cList = new CoffeeConexion\HtmlData\Checkbox($this->name);

        $labelAttr = ['attr1'=>'value1', 'attr2'=>'value2'];
        $cList->setInputAttr($labelAttr);

        $expectedHtml = '<label><input type="checkbox" name="' . $this->name .'[]" value="value1" attr1="value1" attr2="value2" />text1</label>'
            . '<label><input type="checkbox" name="' . $this->name .'[]" value="value2" attr1="value1" attr2="value2" />text2</label>'
            . '<label><input type="checkbox" name="' . $this->name .'[]" value="value3" attr1="value1" attr2="value2" />text3</label>';
        $actualHtml = $cList->checklist($this->checklistOptions, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

}
