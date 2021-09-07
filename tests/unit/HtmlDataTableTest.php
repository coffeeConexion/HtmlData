<?php

use PHPUnit\Framework\TestCase;

/**
 * Test suite for CoffeeConexion\HtmlData\Table.
 *
 * ***
 *
 * Created: May 24, 2021
 * @author Aaron Phillips <aaron.t.phillips@gmail.com>
 * @version 1.0
 * @package CoffeeConexion/HtmlData
 */
class HtmlDataTableTest extends TestCase
{

    /**
     * The default table object.
     * @var object
     */
    protected $table;

    public function setUp(): void
    {
        $this->dt = new CoffeeConexion\HtmlData\Table();

        // Basic data 2D array
        $this->rows = [
            0 => [
                'col1' => 'val1',
                'col2' => 'val2',
                'col3' => 'val3'
            ],
            1 => [
                'col1' => 'val4',
                'col2' => 'val5',
                'col3' => 'val6'
            ],
            2 => [
                'col1' => 'val7',
                'col2' => 'val8',
                'col3' => 'val9'
            ]
        ];
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
    public function That_table_method_generates_well_formed_HTML_table(): void
    {
        $expectedHtml = '<table>
            <tbody>
            <tr>
            <td>val1</td>
            <td>val2</td>
            <td>val3</td>
            </tr>
            <tr>
            <td>val4</td>
            <td>val5</td>
            <td>val6</td>
            </tr>
            <tr>
            <td>val7</td>
            <td>val8</td>
            <td>val9</td>
            </tr>
            </tbody>
            </table>';

        $a = $this->rows;
        $actualHtml = $this->dt->table($a);

        // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertEquals($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setTableAttr_sets_table_attributes(): void
    {
        $expectedHtml = '<table attr1="val1" attr2="val2">';

        $attr = ['attr1'=>'val1', 'attr2'=>'val2'];
        $this->dt->setTableAttr($attr);
        $actualHtml = $this->dt->table($this->rows);

        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setCaption_creates_caption_element(): void
    {
        $captionText = 'Some caption text.';
        $expectedHtml = "<table>
            <caption>$captionText</caption>
            <tbody>";

        $this->dt->setCaption($captionText);
        $actualHtml = $this->dt->table($this->rows);

        // Trims whitespace and tests caption text
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setCaption_creates_caption_element_with_attributes(): void
    {
        // Caption attributes to test
        $captionText = 'Some caption text.';
        $capAttr = ['attr1'=>'val1', 'attr2'=>'val2'];
        $expectedHtml = "<table>
            <caption attr1=\"val1\" attr2=\"val2\">$captionText</caption>
            <tbody>";
        $this->dt->setCaption($captionText, $capAttr);
        $actualHtml = $this->dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

     /** @test */
    public function That_setCaption_throws_exception_for_invalid_caption(): void
    {
        // Caption attributes to test
        $captionText = true;
        $expectedMsg = 'Expecting string for caption, ' . gettype($captionText) . ' given.';

        $actualMsg = '';
        try {
            $this->dt->setCaption($captionText);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setCaption_throws_exception_for_non_array_attribute(): void
    {
        // Caption attributes to test
        $captionText = 'Some caption text.';
        $capAttr = 'Some non-array value';
        $expectedMsg = 'Expecting array for attribute parameter, ' . gettype($capAttr) . ' given.';

        $actualMsg = '';
        try {
            $this->dt->setCaption($captionText, $capAttr);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setCaption_throws_exception_for_invalid_attribute_array(): void
    {
        // Caption attributes to test
        $captionText = 'Some caption text.';
        $capAttr = ['1' => 'some_value'];
        $expectedMsg = 'Expecting valid HTML attribute name, 1 given.';

        $actualMsg = '';
        try {
            $this->dt->setCaption($captionText, $capAttr);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        // Expects exception message for non-array attribute parameter
        $this->assertSame($expectedMsg, $actualMsg);

        $capAttr = ['some_array' => ['sadf' => 'some_value']];
        $expectedMsg = 'Expecting string or integer for some_array, ' . gettype($capAttr['some_array']) . ' given.';

        $actualMsg = '';
        try {
            $this->dt->setCaption($captionText, $capAttr);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        // Expects exception message for array with non-scalar elements
        $this->assertSame($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setTbodyAttr_sets_tbody_attributes(): void
    {
        $tbodyAttr = ['attr1'=>'val1', 'attr2'=>'val2'];
        $expectedHtml = "<table>
            <tbody attr1=\"val1\" attr2=\"val2\">";

        $this->dt->setTbodyAttr($tbodyAttr);
        $actualHtml = $this->dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setThead_sets_thead_element(): void
    {
        $thArray = ['col1', 'col2', 'col3'];
        $expectedHtml = "<table>
            <thead>
            <tr>
                <th>col1</th>
                <th>col2</th>
                <th>col3</th>
            </tr>
            </thead>
            <tbody>";

        $this->dt->setThead($thArray);
        $actualHtml = $this->dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setThead_sets_thead_attributes(): void
    {
        // $thAttr contains the attributes for thhead
        $thAttr = ['attr1'=>'val1', 'attr2'=>'val2'];
        $thArray = ['col1', 'col2', 'col3'];
        $expectedHtml = "<table>
            <thead attr1=\"val1\" attr2=\"val2\">
            <tr>
                <th>col1</th>
                <th>col2</th>
                <th>col3</th>
            </tr>
            </thead>
            <tbody>";

        $this->dt->setThead($thArray, $thAttr);
        $actualHtml = $this->dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setThead_creates_th_element_with_attributes(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $textKey = 'the_text';
        $thArray = [
            ['the_text' => 'col1', 'attr1' => 'val1'],
            ['the_text' => 'col2'],
            ['the_text' => 'col3', 'attr1' => 'val2', 'some_other_attr' => 'some_other_value']
        ];
        $expectedHtml = "<table>
            <thead>
            <tr>
                <th attr1=\"val1\">col1</th>
                <th>col2</th>
                <th attr1=\"val2\" some_other_attr=\"some_other_value\">col3</th>
            </tr>
            </thead>
            <tbody>";

        $dt->setTextKey($textKey);
        $dt->setThead($thArray);
        $actualHtml = $dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_complex_thead_elements_throw_exception_without_text_key(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $this->expectException(\Exception::class);

        $textKey = 'the_text';

        // The third element does not contain the key 'the_text'
        $thArray = [
            ['the_text' => 'col1', 'attr1' => 'val1'],
            ['the_text' => 'col2'],
            ['missing_the_text' => 'col3', 'attr1' => 'val2', 'some_other_attr' => 'some_other_value']
        ];

        $dt->setTextKey($textKey);
        $dt->setThead($thArray);
        $newTable = $dt->table($this->rows);
    }

    /** @test */
    public function That_setFoot_sets_tfoot_element(): void
    {
        $tfArray = ['col1', 'col2', 'col3'];
        $expectedHtml = "<table>
            <tfoot>
            <tr>
                <td>col1</td>
                <td>col2</td>
                <td>col3</td>
            </tr>
            </tfoot>
            <tbody>";

        $this->dt->setTfoot($tfArray);
        $actualHtml = $this->dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setTfoot_creates_td_elements_with_attributes(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $textKey = 'the_text';
        $thArray = [
            ['the_text' => 'col1', 'attr1' => 'val1'],
            ['the_text' => 'col2'],
            ['the_text' => 'col3', 'attr1' => 'val2', 'some_other_attr' => 'some_other_value']
        ];
        $expectedHtml = "<table>
            <tfoot>
            <tr>
                <td attr1=\"val1\">col1</td>
                <td>col2</td>
                <td attr1=\"val2\" some_other_attr=\"some_other_value\">col3</td>
            </tr>
            </tfoot>
            <tbody>";

        $dt->setTextKey($textKey);
        $dt->setTfoot($thArray);
        $actualHtml = $dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_complex_tfoot_elements_throw_exception_without_text_key(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $this->expectException(\Exception::class);

        $textKey = 'the_text';

        // The third element does not contain the key 'the_text'
        $thArray = [
            ['the_text' => 'col1', 'attr1' => 'val1'],
            ['the_text' => 'col2'],
            ['missing_the_text' => 'col3', 'attr1' => 'val2', 'some_other_attr' => 'some_other_value']
        ];

        $dt->setTextKey($textKey);
        $dt->setThead($thArray);
        $newTable = $dt->table($this->rows);
    }

    /** @test */
    public function That_rowCounter_creates_column_for_row_counter(): void
    {
        // Starts counter at 4 and uses custom header text
        $this->dt->rowCounter(4, 'counterColText');

        $expectedHtml = '<table>
            <thead>
            <tr>
                <th>counterColText</th>
                <th>col1</th>
                <th>col2</th>
                <th>col3</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>4</td>
                <td>val1</td>
                <td>val2</td>
                <td>val3</td>
            </tr>
            <tr>
                <td>5</td>
                <td>val4</td>
                <td>val5</td>
                <td>val6</td>
            </tr>
            <tr>
                <td>6</td>
                <td>val7</td>
                <td>val8</td>
                <td>val9</td>
            </tr>
            </tbody>
            </table>';

        $thArray = ['col1', 'col2', 'col3'];
        $this->dt->setThead($thArray);
        $a = $this->rows;
        $actualHtml = $this->dt->table($a);

        // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertEquals($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_rowCounter_set_to_zero_clears_counter(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Empty basic table, no counter
        $a = $this->rows;
        $expectedHtml = $dt->table($a);

        // Sets row counter column
        $dt->rowCounter();

        // Clears row counter column
        $dt->rowCounter(0);
        $actualHtml = $dt->table($a);

        // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertEquals($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_colWd_sets_the_col_style_attribute_width_rule(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Expected thead element
        $expectedHtml = '<colgroup>
                <col style="width: 20%;" />
                <col style="width: 30%;" />
                <col style="width: 50%;" />
            </colgroup>
        ';

        // Sets the width in pixels for each column
        $widthArray = [20, 30, 50];
        $dt->colWd($widthArray);

        // The colWd method only works when using table headers
        $thArray = ['col1', 'col2', 'col3'];
        $dt->setThead($thArray);

        $rowData = $this->rows;
        $actualHtml = $dt->table($rowData);

        // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // Using pixel values for width
        $expectedHtml = '<colgroup>
                <col style="width: 30px;" />
                <col style="width: 40px;" />
                <col style="width: 50px;" />
            </colgroup>
        ';

        // Sets column with using pixels
        $widthArray = [30, 40, 50];
        $dt->colWd($widthArray, true);
        $actualHtml = $dt->table($rowData);

        // Trims whitespace and tests equivalence
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_colWd_throws_exception_invalid_input(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Array with non-integer element
        $widthArray = [0 => 20, 1 => 30, 2 =>'40'];
        $expectedMsg = "All column width values must be positive integers. See element for key '2'.";
        $actualMsg = 'No exception';

        try {
            $dt->colWd($widthArray);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);

        // Array with non-positive integer element
        $widthArray = [0 => 20, 1 => 30, 2 =>0];
        $expectedMsg = "All column width values must be positive integers. See element for key '2'.";
        $actualMsg = 'No exception';

        try {
            $dt->colWd($widthArray);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setHtmlCols_sets_HTML_col_elements(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Array of column values
        $colsArray = [
            ['some_attr' => 'col1', 'attr1' => 'val1'],
            ['some_attr' => 'col2'],
            ['some_attr' => 'col3', 'attr1' => 'val2', 'some_other_attr' => 'some_other_value']
        ];
        $expectedHtml = '<table>
           <colgroup>
                <col some_attr="col1" attr1="val1" />
                <col some_attr="col2" />
                <col some_attr="col3" attr1="val2" some_other_attr="some_other_value" />
            </colgroup>
            <tbody>';

        $dt->setHtmlCols($colsArray);
        $actualHtml = $dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setHtmlCols_throws_exception_for_invalid_input(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Tests non-array imput
        $colsArray = 'abc';
        $expectedMsg = 'Expecting array, ' . gettype($colsArray) . ' given.';
        $actualMsg = 'No exception';

        try {
            $dt->setHtmlCols($colsArray);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertEquals($expectedMsg, $actualMsg);

        // Tests invalid element (array)
        $colsArray = [
            ['some_attr' => 'col1'],
            ['some_attr' => 'col2'],
            ['some_attr' => 'col3',
                'bad_attr' => []
            ]
        ];
        $expectedMsg = 'Expecting string or integer for bad_attr, array given.';
        $actualMsg = 'No exception';

        try {
            $dt->setHtmlCols($colsArray);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }
        $this->assertEquals($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_colWd_works_with_setHtmlCols(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Array of miscellaneous column values
        $colsArray = [
            ['some_attr' => 'col1', 'attr1' => 'val1'],
            ['some_attr' => 'col2'],
            ['some_attr' => 'col3', 'attr1' => 'val2', 'style' => 'color: #ff0000;']
        ];
        $dt->setHtmlCols($colsArray);

        // Sets the width in pixels for each column
        $widthArray = [20, 30, 50];
        $dt->colWd($widthArray);


        $expectedHtml = '<table>
           <colgroup>
                <col some_attr="col1" attr1="val1" style="width: 20%;" />
                <col some_attr="col2" style="width: 30%;" />
                <col some_attr="col3" attr1="val2" style="width: 50%; color: #ff0000;" />
            </colgroup>
            <tbody>';
        $actualHtml = $dt->table($this->rows);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setTextKey_throws_exception_for_invalid_argument(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Array input test
        $var = [];
        $expectedMsg = 'Expecting string or integer, ' . gettype($var) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setTextKey($var);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);

        // Object input test
        $var = new \stdClass();
        $expectedMsg = 'Expecting string or integer, ' . gettype($var) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setTextKey($var);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);

        // Boolean input test
        $var = true;
        $expectedMsg = 'Expecting string or integer, ' . gettype($var) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setTextKey($var);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);

        // Null input test
        $var = null;
        $expectedMsg = 'Expecting string or integer, ' . gettype($var) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setTextKey($var);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);

        // Float input test
        $var = 1.0;
        $expectedMsg = 'Expecting string or integer, ' . gettype($var) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setTextKey($var);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setColumnKeys_throws_exception_for_invalid_argument(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $key = 'abc';
        $value = [];
        $attributes = [$key => $value];
        $expectedMsg = 'Expecting scalar value for ' . $key . ', ' . gettype($value) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setColumnOrder($attributes);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);

        $key = 'abc';
        $value = null;
        $attributes = [$key => $value];
        $expectedMsg = 'Expecting scalar value for ' . $key . ', ' . gettype($value) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setColumnOrder($attributes);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setRowAttrKeys_throws_exception_for_invalid_argument(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $key = 'abc';
        $value = [];
        $attributes = [$key => $value];
        $expectedMsg = 'Expecting scalar value for ' . $key . ', ' . gettype($value) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setRowAttrKeys($attributes);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);

        $key = 'abc';
        $value = null;
        $attributes = [$key => $value];
        $expectedMsg = 'Expecting scalar value for ' . $key . ', ' . gettype($value) . ' given.';
        $actualMsg = 'No exception.';

        try {
            $dt->setRowAttrKeys($attributes);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertEquals($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setColumnOrder_sets_the_order_of_td_elements(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Value 'text2' should be first, even though it's the second element in $rowData
        $expectedHtml = '<tbody>
            <tr>
                <td>text2</td>
                <td>text1</td>
                <td>text3</td>
            </tr>';

        $rowData = [
            ['col1' => 'text1', 'col2' => 'text2', 'col3' => 'text3']
        ];

        $colsArray = ['col2', 'col1','col3'];
        $dt->setColumnOrder($colsArray);
        $actualHtml = $dt->table($rowData);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setColumnOrder_tolerates_missing_keys_by_default(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Second td element has no key
        $expectedHtml = '<tbody>
            <tr>
                <td>text2</td>
                <td></td>
                <td>text1</td>
            </tr>';

        // Key provided for only two columns
        $rowData = [
            ['col1' => 'text1', 'col2' => 'text2']
        ];

        $colsArray = ['col2', 'col3', 'col1'];
        $dt->setColumnOrder($colsArray);
        $actualHtml = $dt->table($rowData);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setColumnOrder_may_use_placeholder_text_for_missing_keys(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        // Second td element has no key
        $expectedHtml = '<tbody>
            <tr>
                <td>text2</td>
                <td>n/a</td>
                <td>text1</td>
            </tr>';

        // Key provided for only two columns
        $rowData = [
            ['col1' => 'text1', 'col2' => 'text2']
        ];

        $colsArray = ['col2', 'col3', 'col1'];
        $dt->setColumnOrder($colsArray, true, 'n/a');
        $actualHtml = $dt->table($rowData);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_setColumnOrder_can_throw_exception_for_missing_values(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $expectedMsg = "No column 'col3' found for row. Check values in setColumnOrder method.";

        // Key provided for only two columns
        $rowData = [
            ['col1' => 'text1', 'col2' => 'text2']
        ];
        $colsArray = ['col2', 'col3', 'col1'];

        // Second parameter throws exception if column key not present in row
        $dt->setColumnOrder($colsArray, false);

        $actualMsg = '';
        try {
            $actualHtml = $dt->table($rowData);
        } catch (\Exception $e) {
            $actualMsg = $e->getMessage();
        }

        $this->assertStringContainsString($expectedMsg, $actualMsg);
    }

    /** @test */
    public function That_setRowAttrKeys_sets_row_attributes(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $raKeys = ['x' => 'class','y' => 'style'];
        $dt->setRowAttrKeys($raKeys);

        $rowData = [
            [
                'a' => 'val1',
                'x' => 'some_class_value',
                'b' => 'val2',
                'y' => 'some style value',
                'c' => 'val3'
            ]
        ];

        $expectedHtml = '<table>
            <tbody>
                <tr>
                    <td>val1</td>
                    <td>some_class_value</td>
                    <td>val2</td>
                    <td>some style value</td>
                    <td>val3</td>
                </tr>
            </tbody>
            </table>
            ';

        $actualHtml = $dt->table($rowData);

        // Trims whitespace and tests caption text and attributes
        $expectedHtml = $this->trimWhitespace($expectedHtml);
        $actualHtml = $this->trimWhitespace($actualHtml);
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }

    /** @test */
    public function That_indent_prepends_whitespace_to_elements(): void
    {
        // Creates a new table because this sets a table property
        $dt = new CoffeeConexion\HtmlData\Table();

        $ws1 = "\n    ";
        $ws2 = "\n        ";
        $ws3 = "\n            ";
        $dt->indent(1); // Sets 1 level (4 spaces per level)

        // Prepends newline for clean start and generates table
        $actualHtml = "\n" . $dt->table($this->rows);

        // Leading elements
        $expectedHtml = "$ws1<table>$ws1<tbody>$ws2<tr>$ws3<td>val1</td>\n";
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // Sample a middle tr element
        $expectedHtml = "$ws2<tr>$ws3<td>val4</td>$ws3<td>val5</td>$ws3<td>val6</td>$ws2</tr>\n";
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // End of table
        $expectedHtml = "$ws2</tr>$ws1</tbody>$ws1</table>";
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // Tests with no indent
        $ws1 = "\n";
        $ws2 = "\n";
        $ws3 = "\n";
        $dt->indent(1, ''); // Sets 1 level (but empty whitespace)

        // Prepends newline for clean start and generates table
        $actualHtml = "\n" . $dt->table($this->rows);

        // Leading elements
        $expectedHtml = "$ws1<table>$ws1<tbody>$ws2<tr>$ws3<td>val1</td>\n";
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // Sample a middle tr element
        $expectedHtml = "$ws2<tr>$ws3<td>val4</td>$ws3<td>val5</td>$ws3<td>val6</td>$ws2</tr>\n";
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // End of table
        $expectedHtml = "$ws2</tr>$ws1</tbody>$ws1</table>";
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // Tests with alternative whitespaces (tabs)
        $ws1 = "\n\t\t";
        $ws2 = "\n\t\t\t";
        $ws3 = "\n\t\t\t\t";
        $dt->indent(2, "\t"); // Sets 1 level (4 spaces per level)

        // Prepends newline for clean start and generates table
        $actualHtml = "\n" . $dt->table($this->rows);

        // Leading elements
        $expectedHtml = "$ws1<table>$ws1<tbody>$ws2<tr>$ws3<td>val1</td>\n";
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // Sample a middle tr element
        $expectedHtml = "$ws2<tr>$ws3<td>val4</td>$ws3<td>val5</td>$ws3<td>val6</td>$ws2</tr>\n";
        $this->assertStringContainsString($expectedHtml, $actualHtml);

        // End of table
        $expectedHtml = "$ws2</tr>$ws1</tbody>$ws1</table>";
        $this->assertStringContainsString($expectedHtml, $actualHtml);
    }
}
