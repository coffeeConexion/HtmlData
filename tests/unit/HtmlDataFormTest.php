<?php

use PHPUnit\Framework\TestCase;

/**
 * Test suite for CoffeeConexion\HtmlData\Form.
 *
 * ***
 *
 * Created: August 9, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */
class HtmlDataFormTest extends TestCase
{

    /**
     * The default form object.
     * @var object
     */
    protected $form;

    public function setUp(): void
    {
        $method = 'some_file.php';
        $action = 'POST';
        $this->form = new CoffeeConexion\HtmlData\Form($method, $action);
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
    public function That_a_form_object_can_be_created(): void
    {
        $action = 'some_file.php';

        $method = 'GET';
        $o = new CoffeeConexion\HtmlData\Form($action, $method);
        $this->assertIsObject($o);

        $method = 'POST';
        $o = new CoffeeConexion\HtmlData\Form($action, $method);
        $this->assertIsObject($o);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_action(): void
    {
        $method = 'get';

        // Action array test
        $action = [];
        $expectedMsg = 'Expecting string for action parameter, array given.';
        $actualMsg = '';

        try {
            $f = new CoffeeConexion\HtmlData\Form($action, $method);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Action integer test
        $action = 1;
        $expectedMsg = 'Expecting string for action parameter, integer given.';
        $actualMsg = '';

        try {
            $f = new CoffeeConexion\HtmlData\Form($action, $method);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_nonstring_method(): void
    {
        $action = 'some_file.php';

        // Method array test
        $method = [];
        $expectedMsg = 'Expecting string for method parameter, array given.';
        $actualMsg = '';

        try {
            $f = new CoffeeConexion\HtmlData\Form($action, $method);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Method integer test
        $method = 1;
        $expectedMsg = 'Expecting string for method parameter, integer given.';
        $actualMsg = '';

        try {
            $f = new CoffeeConexion\HtmlData\Form($action, $method);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_exception_thrown_for_invalid_method_string(): void
    {
        $action = 'some_file.php';
        $method = 'asdf';

        $expectedMsg = 'The form method must be either GET or POST.';
        $actualMsg = '';

        try {
            $f = new CoffeeConexion\HtmlData\Form($action, $method);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }


    /********************************/
    /****** FORM element tests ******/

    /**
     * Tests that form the form method is case insensitive (converts to uppercase).
     * @test
     */
    public function That_the_form_method_case_is_converted_to_uppercase(): void
    {
        $action = 'some_file.php';

        $method = 'get';
        $expectedHtml = '<form action="some_file.php" method="GET">';

        $f = new CoffeeConexion\HtmlData\Form($action, $method);
        $actualHtml = $f->openForm();
        $this->assertSame($expectedHtml, $actualHtml);

        $method = 'post';
        $expectedHtml = '<form action="some_file.php" method="POST">';

        $f = new CoffeeConexion\HtmlData\Form($action, $method);
        $actualHtml = $f->openForm();
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_openForm_creates_html_attributes_from_array(): void
    {
        $a = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<form action="some_file.php" method="POST" some_attr="some_value" some_attr2="some_value2">';

        $actualHtml = $this->form->openForm($a);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_openForm_attribute_parameter_ignores_method_and_action_elements(): void
    {
        $a = ['action' => 'some_other_action', 'method' => 'some_other_method'];
        $expectedHtml = '<form action="some_file.php" method="POST">';

        $actualHtml = $this->form->openForm($a);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_closeForm_returns_element(): void
    {
        $expectedHtml = '</form>';
        $actualHtml = $this->form->closeForm();
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
        $expectedMsg = "Expecting string for attribute key 0, integer given.";
        $actualMsg = '';

        try {
            $s = $this->form->openForm($a);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Array for value test
        $a = ['a' => ['a']]; // Nested array
        $expectedMsg = "Expecting string or NULL for value of 'a' attribute, array given.";
        $actualMsg = '';


        try {
            $s = $this->form->openForm($a);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }


    /**********************************/
    /****** SUBMIT element tests ******/

    /** @test */
    public function That_submitButton_creates_element(): void
    {
        $value = 'some_text';
        $expectedHtml = '<input type="submit" value="some_text" />';
        $actualHtml = $this->form->submitButton($value);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_submitButton_throws_exception_for_invalid_value_type(): void
    {
        // Value array test
        $value = [];
        $expectedMsg = 'Expecting string for value parameter, array given.';
        $actualMsg = '';

        try {
            $s = $this->form->submitButton($value);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Value integer test
        $value = 1;
        $expectedMsg = 'Expecting string for value parameter, integer given.';
        $actualMsg = '';

        try {
            $s = $this->form->submitButton($value);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_submitButton_creates_html_attributes_from_array(): void
    {
        $a = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="submit" value="some_text" some_attr="some_value" some_attr2="some_value2" />';

        $actualHtml = $this->form->submitButton('some_text', $a);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_submitButton_attr_parameter_ignores_reserved_attributes(): void
    {
        $a = ['type' => 'some_other_type', 'value' => 'some_other_text'];
        $expectedHtml = '<input type="submit" value="some_text" />';

        $actualHtml = $this->form->submitButton('some_text', $a);
        $this->assertSame($expectedHtml, $actualHtml);
    }


    /*********************************/
    /****** RESET element tests ******/

    /** @test */
    public function That_resetButton_creates_element(): void
    {
        $value = 'some_text';
        $expectedHtml = '<input type="reset" value="some_text" />';
        $actualHtml = $this->form->resetButton($value);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_resetButton_throws_exception_for_invalid_value_type(): void
    {
        // Value array test
        $value = [];
        $expectedMsg = 'Expecting string for value parameter, array given.';
        $actualMsg = '';

        try {
            $s = $this->form->resetButton($value);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Value integer test
        $value = 1;
        $expectedMsg = 'Expecting string for value parameter, integer given.';
        $actualMsg = '';

        try {
            $s = $this->form->resetButton($value);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_resetButton_creates_html_attributes_from_array(): void
    {
        $a = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="reset" value="some_text" some_attr="some_value" some_attr2="some_value2" />';

        $actualHtml = $this->form->resetButton('some_text', $a);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_resetButton_attr_parameter_ignores_reserved_attributes(): void
    {
        $a = ['type' => 'some_other_type', 'value' => 'some_other_text'];
        $expectedHtml = '<input type="reset" value="some_text" />';

        $actualHtml = $this->form->resetButton('some_text', $a);
        $this->assertSame($expectedHtml, $actualHtml);
    }


    /**********************************/
    /****** HIDDEN element tests ******/

    /** @test */
    public function That_hiddenInput_creates_element(): void
    {
        $name = 'some_name';
        $value = 'some_text';
        $expectedHtml = '<input type="hidden" name="some_name" value="some_text" />';
        $actualHtml = $this->form->hiddenInput($name, $value);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_hiddenInput_throws_exception_for_invalid_value_type(): void
    {
        $name = 'some_name';

        // Value array test
        $value = [];
        $expectedMsg = 'The hidden field value must be scalar.';
        $actualMsg = '';

        try {
            $s = $this->form->hiddenInput($name, $value);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_hiddenInput_creates_html_attributes_from_array(): void
    {
        $name = 'some_name';
        $value = 'some_text';
        $a = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<input type="hidden" name="some_name" value="some_text" some_attr="some_value" some_attr2="some_value2" />';

        $actualHtml = $this->form->hiddenInput($name, $value, $a);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_hiddenInput_attr_parameter_ignores_reserved_attributes(): void
    {
        $name = 'some_name';
        $value = 'some_text';
        $a = ['type' => 'some_other_type', 'name' => 'some_other_name', 'value' => 'some_other_text'];
        $expectedHtml = '<input type="hidden" name="some_name" value="some_text" />';

        $actualHtml = $this->form->hiddenInput($name, $value, $a);
        $this->assertSame($expectedHtml, $actualHtml);
    }


    /************************************/
    /****** FIELDSET element tests ******/

    /** @test */
    public function That_fieldset_methods_create_html_elements(): void
    {
        $expectedHtml = '<form action="some_file.php" method="POST">
            <fieldset>
            </fieldset>
            </form>';

        $actualHtml = $this->form->openForm() . "\n";
        $actualHtml .= $this->form->openFieldset() . "\n";
        $actualHtml .= $this->form->closeFieldset() . "\n";
        $actualHtml .= $this->form->closeForm();

        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_openFieldset_creates_html_attributes(): void
    {
        $a = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<fieldset some_attr="some_value" some_attr2="some_value2">';
        $actualHtml = $this->form->openFieldset($a);
        $this->assertSame($expectedHtml, $actualHtml);
    }


    /**********************************/
    /****** LEGEND element tests ******/

    /** @test */
    public function That_legend_creates_element(): void
    {
        $text = 'some_text';
        $expectedHtml = "<legend>$text</legend>";
        $actualHtml = $this->form->legend($text);
        $this->assertSame($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_legend_throws_exception_for_invalid_text_type(): void
    {
        // Text array test
        $text = [];
        $expectedMsg = 'Expecting string for legend text, array given.';
        $actualMsg = '';

        try {
            $s = $this->form->legend($text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);

        // Text integer test
        $text = 1;
        $expectedMsg = 'Expecting string for legend text, integer given.';
        $actualMsg = '';

        try {
            $s = $this->form->legend($text);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_legend_creates_html_attributes(): void
    {
        $text = 'Some text';
        $a = ['some_attr' => 'some_value', 'some_attr2' => 'some_value2'];
        $expectedHtml = '<legend some_attr="some_value" some_attr2="some_value2">' . $text . '</legend>';
        $actualHtml = $this->form->legend($text, $a);
        $this->assertSame($expectedHtml, $actualHtml);
    }
}
