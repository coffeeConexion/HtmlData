<?php

use PHPUnit\Framework\TestCase;

/**
 * Test suite for CoffeeConexion\HtmlData\Textbox.
 *
 * ***
 *
 * Created: August 9, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */
class HtmlDataTextboxTest extends TestCase
{

    /**
     * The default textbox object.
     * @var object
     */
    protected $tb;

    /**
     * The textbox name attribute.
     * @var string
     */
    protected $name;

    public function setUp(): void
    {
        $this->name = 'some_name';
        $this->tb = new CoffeeConexion\HtmlData\Textbox($this->name);
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
    public function That_a_textbox_object_can_be_created(): void
    {
        $o = new CoffeeConexion\HtmlData\Textbox($this->name);
        $this->assertIsObject($o);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_name(): void
    {
        // Name array test
        $name = [];

        $expectedMsg = 'Expecting string, array given.';
        $actualMsg = '';

        try {
            $o = new CoffeeConexion\HtmlData\Textbox($name);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Name integer test
        $name = 1;
        $expectedMsg = 'Expecting string, integer given.';
        $actualMsg = '';

        try {
            $o = new CoffeeConexion\HtmlData\Textbox($name);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_textbox_first_parameter_accepts_scalar(): void
    {
        $p1 = 'some_text';
        $expectedHtml = '<input type="text" name="some_name" value="some_text" />';
        $actualHtml = $this->tb->textbox($p1);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_textbox_first_parameter_accepts_attribute_array(): void
    {
        $p1 = ['value' => 'some_text', 'some_attr' => 'some_value'];
        $expectedHtml = '<input type="text" name="some_name" value="some_text" some_attr="some_value" />';
        $actualHtml = $this->tb->textbox($p1);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_textbox_second_parameter_accepts_attribute_array(): void
    {
        $text = 'some_text';
        $p2 = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="text" name="some_name" value="some_text" some_attr="some_value" some_attr2="some_value2" />';
        $actualHtml = $this->tb->textbox($text, $p2);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_textbox_second_parameter_ignores_value_attribute(): void
    {
        $text = 'some_text';
        $p2 = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2', 'value' => 'ignored_text_value'];
        $expectedHtml = '<input type="text" name="some_name" value="some_text" some_attr="some_value" some_attr2="some_value2" />';
        $actualHtml = $this->tb->textbox($text, $p2);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_textbox_ignores_reserved_attributes(): void
    {
        // First parameter test
        $p1 = ['type' => 'some_other_type', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="text" name="some_name" some_attr2="some_value2" />';
        $actualHtml = $this->tb->textbox($p1);

        $this->assertSame($expectedHtml, $actualHtml);

        // Second parameter test
        $text = 'some_text';
        $p2 = ['type' => 'type', 'value' =>'some_other_text', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="text" name="some_name" value="some_text" some_attr2="some_value2" />';
        $actualHtml = $this->tb->textbox($text, $p2);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_textbox_creates_textarea_element(): void
    {
        $text = 'some_text';
        $expectedHtml = '<textarea name="' . $this->name . '">some_text</textarea>';
        $actualHtml = $this->tb->textbox($text, null, true);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_textarea_method_creates_textarea_element(): void
    {
        $text = 'some_text';
        $expectedHtml = '<textarea name="' . $this->name . '">some_text</textarea>';
        $actualHtml = $this->tb->textarea($text, null);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_textarea_accepts_attribute_array(): void
    {
        $text = 'some_text';
        $a = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<textarea name="' . $this->name . '" some_attr="some_value" some_attr2="some_value2">some_text</textarea>';
        $actualHtml = $this->tb->textbox($text, $a, true);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /**
     * The private formatAttr and validateAttr methods are used for multiple methods, so this test covers all.
     * @test 
     */
    public function That_attribute_arrays_are_validated(): void
    {
        // Integer key test
        $a = [1 => 'some_value']; // Nested array
        $expectedMsg = 'Expecting string for attribute key, integer given.';
        $actualMsg = '';

        try {
            $s = $this->tb->textbox($a);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Array for value test
        $a = ['a' => ['a']]; // Nested array
        $expectedMsg = "Expecting string or NULL for value of 'a' attribute, array given.";
        $actualMsg = '';

        try {
            $s = $this->tb->textbox($a);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_a_label_element_can_be_created(): void
    {
        $text = 'some_text';
        $expectedHtml = '<label for="' . $this->name . '">' . $text . '</label>';
        $actualHtml = $this->tb->label($text);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_label_creates_html_attributes_from_array(): void
    {
        $text = 'some_text';
        $a = ['value' => 'some_text', 'some_attr' => 'some_value'];
        $expectedHtml = '<label for="' . $this->name . '" value="some_text" some_attr="some_value">' . $text . '</label>';
        $actualHtml = $this->tb->label($text, $a);

        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_label_ignores_reserved_attributes(): void
    {
        $text = 'some_text';
        $a = ['for' => 'some_other_for', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<label for="' . $this->name . '" some_attr2="some_value2">' . $text . '</label>';
        $actualHtml = $this->tb->label($text, $a);

        $this->assertSame($expectedHtml, $actualHtml);
    }

}
