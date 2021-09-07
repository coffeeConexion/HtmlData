<?php

use PHPUnit\Framework\TestCase;

/**
 * Test suite for CoffeeConexion\HtmlData\Input.
 *
 * ***
 *
 * Created: August 21, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */
class HtmlDataInputTest extends TestCase
{

    /**
     * The default textbox object.
     * @var object
     */
    protected $iObject;

    /**
     * The input type attribute.
     * @var string
     */
    protected $type;

    /**
     * The input name attribute.
     * @var string
     */
    protected $name;

    protected $validTypes = [
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

    public function setUp(): void
    {
        $this->name = 'some_name';
        $this->type = 'date';
        $this->iObject = new CoffeeConexion\HtmlData\Input($this->name, $this->type);
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
    public function That_an_input_object_can_be_created(): void
    {
        $name = 'some_name';
        $type = 'color';

        $o = new CoffeeConexion\HtmlData\Input($this->name, $this->type);
        $this->assertIsObject($o);
    }

    /** @test */
    public function That_input_method_creates_html_input_element(): void
    {
        $attributes = ['attr1' => 'value1', 'attr2' => 'value2'];

        $expectedHtml = '<input type="' . $this->type .'" name="' . $this->name . '" />';
        $actualHtml = $this->iObject->input();

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_input_first_parameter_accepts_scalar(): void
    {
        $attributes = ['attr1' => 'value1', 'attr2' => 'value2'];

        $expectedHtml = '<input type="' . $this->type .'" name="' . $this->name . '" value="value1" />';
        $actualHtml = $this->iObject->input('value1');

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_input_first_parameter_accepts_attribute_array(): void
    {
        $p1 = ['value' => 'some_text', 'some_attr' => 'some_value'];
        $expectedHtml = '<input type="date" name="some_name" value="some_text" some_attr="some_value" />';
        $actualHtml = $this->iObject->input($p1);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_input_second_parameter_accepts_attribute_array(): void
    {
        $text = 'some_text';
        $p2 = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="date" name="some_name" value="some_text" some_attr="some_value" some_attr2="some_value2" />';
        $actualHtml = $this->iObject->input($text, $p2);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_input_second_parameter_ignores_value_attribute(): void
    {
        $text = 'some_text';
        $p2 = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2', 'value' => 'ignored_text_value'];
        $expectedHtml = '<input type="date" name="some_name" value="some_text" some_attr="some_value" some_attr2="some_value2" />';
        $actualHtml = $this->iObject->input($text, $p2);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_input_ignores_reserved_attributes(): void
    {
        // First parameter test
        $p1 = ['type' => 'some_other_type', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="date" name="some_name" some_attr2="some_value2" />';
        $actualHtml = $this->iObject->input($p1);

        $this->assertSame($expectedHtml, $actualHtml);

        // Second parameter test
        $text = 'some_text';
        $p2 = ['type' => 'type', 'value' =>'some_other_text', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="date" name="some_name" value="some_text" some_attr2="some_value2" />';
        $actualHtml = $this->iObject->input($text, $p2);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_label_element_for_input_can_be_created(): void
    {
        $text = 'some_text';
        $expectedHtml = '<label for="' . $this->name . '">' . $text . '</label>';
        $actualHtml = $this->iObject->label($text);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_input_label_creates_html_attributes_from_array(): void
    {
        $text = 'some_text';
        $a = ['value' => 'some_text', 'some_attr' => 'some_value'];
        $expectedHtml = '<label for="' . $this->name . '" value="some_text" some_attr="some_value">' . $text . '</label>';
        $actualHtml = $this->iObject->label($text, $a);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_input_label_ignores_reserved_attributes(): void
    {
        $text = 'some_text';
        $a = ['for' => 'some_other_for', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<label for="' . $this->name . '" some_attr2="some_value2">' . $text . '</label>';
        $actualHtml = $this->iObject->label($text, $a);

        $this->assertSame($expectedHtml, $actualHtml);
    }

}
