<?php

use PHPUnit\Framework\TestCase;

/**
 * Test suite for CoffeeConexion\HtmlData\Radio.
 *
 * ***
 *
 * Created: August 19, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */
class HtmlDataRadioTest extends TestCase
{

    /**
     * The default radio object.
     * @var object
     */
    protected $radio;

    /**
     * The radio input element name attribute.
     * @var string
     */
    protected $name = 'some_name';

    /**
     * Standard radio value key.
     * @var string
     */
    protected $valueKey = 'value_key';

    /**
     * Standard radio text key.
     * @var string
     */
    protected $textKey = 'text_key';

    /**
     * The default radio options array.
     * @var array
     */
    protected $radioOptions = [
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
        $this->rButton = new CoffeeConexion\HtmlData\Radio($this->name);
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
    public function That_a_radio_object_can_be_created(): void
    {
        $o = new CoffeeConexion\HtmlData\Radio($this->name);
        $this->assertIsObject($o);
    }

    /** @test */
    public function That_radio_method_creates_html_radio_element(): void
    {
        $expectedHtml = '<label><input type="radio" name="' . $this->name . '" value="value1" />text1</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value2" />text2</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value3" />text3</label>';
        $actualHtml = $this->rButton->radio($this->radioOptions, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_radio_can_use_alternate_separator_character(): void
    {
        $separator = 'abc';

        $expectedHtml = '<label><input type="radio" name="' . $this->name . '" value="value1" />text1</label>'
            . $separator . '<label><input type="radio" name="' . $this->name . '" value="value2" />text2</label>'
            . $separator . '<label><input type="radio" name="' . $this->name . '" value="value3" />text3</label>';
        $actualHtml = $this->rButton->radio($this->radioOptions, $this->valueKey, $this->textKey, $separator);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setChecked_sets_the_checked_option(): void
    {
        $r = new CoffeeConexion\HtmlData\Radio($this->name);
        $r->setChecked('value2');

        $expectedHtml = '<label><input type="radio" name="' . $this->name . '" value="value1" />text1</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value2" checked="checked" />text2</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value3" />text3</label>';
        $actualHtml = $r->radio($this->radioOptions, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_attributes_may_be_set(): void
    {
        $options = $this->radioOptions;
        $options[0]['some_attr1'] = 'some_val1';
        $options[2]['some_attr3'] = 'some_val3';

        $expectedHtml = '<label some_attr1="some_val1"><input type="radio" name="' . $this->name . '" value="value1" />text1</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value2" />text2</label>'
            . '<label some_attr3="some_val3"><input type="radio" name="' . $this->name . '" value="value3" />text3</label>';
        $actualHtml = $this->rButton->radio($options, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_attributes_may_be_set_with_checked_option(): void
    {
        $r = new CoffeeConexion\HtmlData\Radio($this->name);
        $r->setChecked('value2');

        $options = $this->radioOptions;
        $options[0]['some_attr1'] = 'some_val1';
        $options[2]['some_attr3'] = 'some_val3';

        $expectedHtml = '<label some_attr1="some_val1"><input type="radio" name="' . $this->name . '" value="value1" />text1</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value2" checked="checked" />text2</label>'
            . '<label some_attr3="some_val3"><input type="radio" name="' . $this->name . '" value="value3" />text3</label>';
        $actualHtml = $r->radio($options, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setLabelAttr_sets_label_attributes_for_all_options(): void
    {
        $r = new CoffeeConexion\HtmlData\Radio($this->name);

        $value = 'value_key';
        $text = 'text_key';
        $r->setLabelAttr(['all_options_attr1' => 'all_options_value1', 'all_options_attr2' => 'all_options_value2']);

        // Uses input attributes with option attributes to test for interference
        $options = $this->radioOptions;
        $options[0]['some_attr1'] = 'some_val1';
        $options[2]['some_attr3'] = 'some_val3';

        $expectedHtml = '<label all_options_attr1="all_options_value1" all_options_attr2="all_options_value2" some_attr1="some_val1"><input type="radio" name="' . $this->name . '" value="value1" />text1</label>'
            . '<label all_options_attr1="all_options_value1" all_options_attr2="all_options_value2"><input type="radio" name="' . $this->name . '" value="value2" />text2</label>'
            . '<label all_options_attr1="all_options_value1" all_options_attr2="all_options_value2" some_attr3="some_val3"><input type="radio" name="' . $this->name . '" value="value3" />text3</label>';
        $actualHtml = $r->radio($options, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setInputAttr_sets_input_element_attributes(): void
    {
        $r = new CoffeeConexion\HtmlData\Radio($this->name);

        $value = 'value_key';
        $text = 'text_key';
        $r->setInputAttr(['all_options_attr1' => 'all_options_value1', 'all_options_attr2' => 'all_options_value2']);

        // Uses setChecked method to test for interference
        $r->setChecked('value2');

         $expectedHtml = '<label><input type="radio" name="' . $this->name . '" value="value1" all_options_attr1="all_options_value1" all_options_attr2="all_options_value2" />text1</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value2" all_options_attr1="all_options_value1" all_options_attr2="all_options_value2" checked="checked" />text2</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value3" all_options_attr1="all_options_value1" all_options_attr2="all_options_value2" />text3</label>';
        $actualHtml = $r->radio($this->radioOptions, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_reserved_input_attributes_are_ignored(): void
    {
        $r = new CoffeeConexion\HtmlData\Radio($this->name);

        $r->setInputAttr(['all_options_attr1' => 'all_options_value1',
            'type' => 'some_other_type',
            'name' => 'some_other_name',
            'value' => 'some_other_value',
            'checked' => 'checked' // Must use setChecked method
        ]);

        // Uses setChecked method to test for interference
        $r->setChecked('value2');

         $expectedHtml = '<label><input type="radio" name="' . $this->name . '" value="value1" all_options_attr1="all_options_value1" />text1</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value2" all_options_attr1="all_options_value1" checked="checked" />text2</label>'
            . '<label><input type="radio" name="' . $this->name . '" value="value3" all_options_attr1="all_options_value1" />text3</label>';
        $actualHtml = $r->radio($this->radioOptions, $this->valueKey, $this->textKey);

        $this->assertSame($expectedHtml, $actualHtml);
    }



    /********************************/
    /****** Exception  tests ********/

    /** @test */
    public function That_exception_thrown_for_nonstring_text_parameter(): void
    {
        // Text array test
        $badText = [];

        $expectedMsg = 'Expecting string for text parameter, array given.';
        $actualMsg = '';

        try {
            $this->rButton->radio($this->radioOptions, $this->valueKey, $badText);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Text null test
        $badText = null;
        $expectedMsg = 'Expecting string for text parameter, NULL given.';
        $actualMsg = '';

        try {
            $this->rButton->radio($this->radioOptions, $this->valueKey, $badText);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_value_parameter(): void
    {
        // Value array test
        $badValue = [];

        $expectedMsg = 'Expecting string for value parameter, array given.';
        $actualMsg = '';

        try {
            $this->rButton->radio($this->radioOptions, $badValue, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Value null test
        $badValue = null;
        $expectedMsg = 'Expecting string for value parameter, NULL given.';
        $actualMsg = '';

        try {
            $this->rButton->radio($this->radioOptions, $badValue, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_nonarray_option_parameter(): void
    {
        // Options string test
        $badOptions = 'abc';

        $expectedMsg = 'Option array must be a 2-dimensional array, containing arrays with the text and HTML value attribute.';
        $actualMsg = '';

        try {
            $this->rButton->radio($badOptions, $this->valueKey, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Options null test
        $badOptions = null;
        $actualMsg = '';

        try {
            $this->rButton->radio($badOptions, $this->valueKey, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_option_missing_value_key_in_options(): void
    {
        $value = 'value_key';
        $text = 'text_key';
        $badOptions = ['wrong_value_key' => 'some_value', 'text_key' => 'some_text'];

        $expectedMsg = 'Option array must be a 2-dimensional array, containing arrays with the text and HTML value attribute.';
        $actualMsg = '';

        try {
            $this->rButton->radio($badOptions, $value, $text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_option_missing_text_key_in_options(): void
    {
        $badOptions = ['value_key' => 'some_value', 'wrong_text_key' => 'some_text'];

        $expectedMsg = 'Option array must be a 2-dimensional array, containing arrays with the text and HTML value attribute.';
        $actualMsg = '';

        try {
            $this->rButton->radio($badOptions, $this->valueKey, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_value_in_options(): void
    {
        $expectedMsg = 'Key ' . $this->valueKey . ' was not set for option 0.';

        // Bad array value in options test
        $badOptions = [
            ['bad_value_key' => 'some_value', 'text_key' => 'some_text']
        ];
        $actualMsg = '';

        try {
            $this->rButton->radio($badOptions, $this->valueKey, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Bad null value in options test
        $badOptions = [
            ['bad_value_key' => 'some_value', 'text_key' => 'some_text']
        ];
        $actualMsg = '';

        try {
            $this->rButton->radio($badOptions, $this->valueKey, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_text_in_options(): void
    {
        //$valueKey = 'value_key';
        //$textKey = 'text_key';
        $expectedMsg = 'Key ' . $this->textKey . ' was not set for option 0.';

        // Bad array value in options test
        $badOptions = [
            ['value_key' => 'some_value', 'wrong_text_key' => 'some_text']
        ];
        $actualMsg = '';

        try {
            $this->rButton->radio($badOptions, $this->valueKey, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Bad null value in options test
        $badOptions = [
            ['value_key' => 'some_value', 'wrong_text_key' => 'some_text']
        ];
        $actualMsg = '';

        try {
            $this->rButton->radio($badOptions, $this->valueKey, $this->textKey);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

}
