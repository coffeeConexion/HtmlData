<?php

use PHPUnit\Framework\TestCase;

/**
 * Test suite for CoffeeConexion\HtmlData\Select.
 *
 * ***
 *
 * Created: August 21, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */
class HtmlDataSelectTest extends TestCase
{

    /**
     * The default Select object.
     * @var object
     */
    protected $sList;

    /**
     * The name attribute.
     * @var string
     */
    protected $name = 'some_name';

    /**
     * The name attribute.
     * @var string
     */
    protected $text = 'some_text';

    /**
     * The name attribute.
     * @var string
     */
    protected $value = 'some_value';

    /**
     * The options array.
     * @var array
     */
    protected $options = [
        ['some_value' => 'value1', 'some_text' => 'text1'],
        ['some_value' => 'value2', 'some_text' => 'text2'],
        ['some_value' => 'value3', 'some_text' => 'text3']
    ];


    public function setUp(): void
    {
        $this->sList = new CoffeeConexion\HtmlData\Select($this->name);
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
    public function That_a_selection_list_object_can_be_created(): void
    {
        $o = new CoffeeConexion\HtmlData\Select($this->name);
        $this->assertIsObject($o);
    }

    /** @test */
    public function That_selectionList_method_creates_selection_list(): void
    {
        $value = 'some_value';
        $text = 'some_text';

        $expectedHtml = '<select name="' . $this->name . '">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2">text2</option>'
            ."\n" . '<option value="value3">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $this->sList->selectionList($this->options, $value, $text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_listAttributes_method_sets_select_element_attributes(): void
    {
        $attributes = ['attr1' => 'value1', 'attr2' => 'value2'];

        $s = new CoffeeConexion\HtmlData\Select('some_name');
        $s->listAttributes($attributes);

        $expectedHtml = '<select name="' . $this->name . '" attr1="value1" attr2="value2">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2">text2</option>'
            ."\n" . '<option value="value3">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setBlank_method_sets_blank_HTML_option_element(): void
    {
        $s = new CoffeeConexion\HtmlData\Select('some_name');
        $s->setBlank();

        $expectedHtml = '<select name="' . $this->name . '">'
            ."\n" . '<option></option>'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2">text2</option>'
            ."\n" . '<option value="value3">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setSelected_method_sets_single_selected_option_element(): void
    {
        $s = new CoffeeConexion\HtmlData\Select($this->name);
        $s->setSelected('value2');

        $expectedHtml = '<select name="' . $this->name . '">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2" selected="selected">text2</option>'
            ."\n" . '<option value="value3">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setSelected_method_sets_multiple_selected_option_elements(): void
    {
        $s = new CoffeeConexion\HtmlData\Select($this->name);
        $s->setSelected(['value2', 'value3']);

        $expectedHtml = '<select name="' . $this->name . '[]" multiple="multiple">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2" selected="selected">text2</option>'
            ."\n" . '<option value="value3" selected="selected">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setSelected_method_single_value_works_with_select_element_attributes(): void
    {
        $s = new CoffeeConexion\HtmlData\Select($this->name);
        $attributes = ['attr1' => 'value1', 'attr2' => 'value2'];

        $s->listAttributes($attributes);
        $s->setSelected('value2');

        $expectedHtml = '<select name="' . $this->name . '" attr1="value1" attr2="value2">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2" selected="selected">text2</option>'
            ."\n" . '<option value="value3">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setSelected_method_multiple_value_works_with_select_element_attributes(): void
    {
        $s = new CoffeeConexion\HtmlData\Select($this->name);
        $attributes = ['attr1' => 'value1', 'attr2' => 'value2'];

        $s->listAttributes($attributes);
        $s->setSelected(['value2', 'value3']);

        $expectedHtml = '<select name="' . $this->name . '[]" multiple="multiple" attr1="value1" attr2="value2">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2" selected="selected">text2</option>'
            ."\n" . '<option value="value3" selected="selected">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setSelected_method_multiple_value_works_with_empty_select_array(): void
    {
        $s = new CoffeeConexion\HtmlData\Select($this->name);
        $attributes = ['attr1' => 'value1', 'attr2' => 'value2'];

        $s->listAttributes($attributes);
        $s->setSelected([]);

        $expectedHtml = '<select name="' . $this->name . '[]" multiple="multiple" attr1="value1" attr2="value2">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2">text2</option>'
            ."\n" . '<option value="value3">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setSelected_method_multiple_value_ignores_invalid_select_array_elements(): void
    {
        $s = new CoffeeConexion\HtmlData\Select($this->name);
        $attributes = ['attr1' => 'value1', 'attr2' => 'value2'];

        $s->listAttributes($attributes);
        $s->setSelected([null, true, []]);

        $expectedHtml = '<select name="' . $this->name . '[]" multiple="multiple" attr1="value1" attr2="value2">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2">text2</option>'
            ."\n" . '<option value="value3">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setSelected_silently_ignores_false_and_null_values(): void
    {
        $s = new CoffeeConexion\HtmlData\Select($this->name);

        // Type false test
        $s->setSelected(false);

        $expectedHtml = '<select name="' . $this->name . '">'
            ."\n" . '<option value="value1">text1</option>'
            ."\n" . '<option value="value2">text2</option>'
            ."\n" . '<option value="value3">text3</option>'
            ."\n" . '</select>';
        $actualHtml = $s->selectionList($this->options, $this->value, $this->text);

         // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);

        // Tyle null test
        $s->setSelected(null);
        $actualHtml = $this->trimWhitespace($actualHtml);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setSelected_throws_exception_for_invalid_parameter_type(): void
    {
        $s = new CoffeeConexion\HtmlData\Select($this->name);

        // Boolean test
        $badSelected = true;

        $expectedMsg = 'Expecting string, integer, or array for selected parameter, ' . gettype($badSelected) . ' given.';
        $actualMsg = '';

        try {
            $s->setSelected($badSelected);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Object test
        $badSelected = new stdClass();

        $expectedMsg = 'Expecting string, integer, or array for selected parameter, ' . gettype($badSelected) . ' given.';
        $actualMsg = '';

        try {
            $s->setSelected($badSelected);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_selectionList_method_throws_exception_for_invalid_value_parameter(): void
    {
        // Type integer test
        $badValue = 1;

        $expectedMsg = 'Expecting string for value parameter, ' . gettype($badValue) . ' given.';
        $actualMsg = '';

        try {
            $this->sList->selectionList($this->options, $badValue, $this->text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Type array test
        $badValue = [];

        $expectedMsg = 'Expecting string for value parameter, ' . gettype($badValue) . ' given.';
        $actualMsg = '';

        try {
            $this->sList->selectionList($this->options, $badValue, $this->text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_selectionList_method_throws_exception_for_invalid_text_parameter(): void
    {
        // Type integer test
        $badText = 1;

        $expectedMsg = 'Expecting string for text parameter, ' . gettype($badText) . ' given.';
        $actualMsg = '';

        try {
            $this->sList->selectionList($this->options, $this->value, $badText);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Type array test
        $badText = [];

        $expectedMsg = 'Expecting string for text parameter, ' . gettype($badText) . ' given.';
        $actualMsg = '';

        try {
            $this->sList->selectionList($this->options, $this->value, $badText);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_selectionList_method_throws_exception_for_option_missing_value_key(): void
    {
        // Type integer test
        $badOptions = [
            ['some_value' => 'value1', 'some_text' => 'text1'],
            ['wrong_value_key' => 'value2', 'some_text' => 'text2'],
            ['some_value' => 'value3', 'some_text' => 'text3']
        ];

        //$expectedMsg = 'The value provided (' . $valueKey . ') does not refer to a key in the array.';
        $expectedMsg = 'Option missing value key for (' . $this->value . ').';
        $actualMsg = '';

        try {
            $this->sList->selectionList($badOptions, $this->value, $this->text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_selectionList_method_throws_exception_for_option_missing_text_key(): void
    {
        // Type integer test
        $badOptions = [
            ['some_value' => 'value1', 'some_text' => 'text1'],
            ['some_value' => 'value2', 'wrong_text_key' => 'text2'],
            ['some_value' => 'value3', 'some_text' => 'text3']
        ];

        //$expectedMsg = 'The value provided (' . $valueKey . ') does not refer to a key in the array.';
        $expectedMsg = 'Option missing text key for (' . $this->text . ').';
        $actualMsg = '';

        try {
            $this->sList->selectionList($badOptions, $this->value, $this->text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_a_label_element_can_be_created(): void
    {
        $labelText = 'Some label text';

        $expectedHtml = '<label for="some_name">' . $labelText . '</label>';
        $actualHtml = $this->sList->label($labelText);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_a_label_element_can_be_created_with_attributes(): void
    {
        $labelText = 'Some label text';
        $labelAttributes = ['attr1' => 'value1', 'attr2' => 'value2'];

        $expectedHtml = '<label for="some_name" attr1="value1" attr2="value2">' . $labelText . '</label>';
        $actualHtml = $this->sList->label($labelText, $labelAttributes);

        $this->assertSame($expectedHtml, $actualHtml);
    }

}
