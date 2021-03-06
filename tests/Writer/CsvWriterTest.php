<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Exporter\Test\Writer;

use Exporter\Test\AbstractTypedWriterTestCase;
use Exporter\Writer\CsvWriter;

class CsvWriterTest extends AbstractTypedWriterTestCase
{
    protected $filename;

    public function setUp()
    {
        parent::setUp();
        $this->filename = 'foobar.csv';

        if (is_file($this->filename)) {
            unlink($this->filename);
        }
    }

    public function tearDown()
    {
        if (is_file($this->filename)) {
            unlink($this->filename);
        }
    }

    public function testInvalidDataFormat()
    {
        $this->expectException(\Exporter\Exception\InvalidDataFormatException::class);

        $writer = new CsvWriter($this->filename, ',', '', '\\', false);
        $writer->open();

        $writer->write(['john "2', 'doe', '1']);
    }

    public function testEnclosureFormating()
    {
        $writer = new CsvWriter($this->filename, ',', '"', '\\', false);
        $writer->open();

        $writer->write([' john , ""2"', 'doe', '1']);

        $writer->close();

        $expected = '" john , """"2""",doe,1';

        $this->assertEquals($expected, trim(file_get_contents($this->filename)));
    }

    public function testEscapeFormating()
    {
        $writer = new CsvWriter($this->filename, ',', '"', '/', false);

        $writer->open();

        $writer->write(['john', 'doe', '\\', '/']);

        $writer->close();

        $expected = 'john,doe,\,"/"';

        $this->assertEquals($expected, trim(file_get_contents($this->filename)));
    }

    public function testWithTerminate()
    {
        $writer = new CsvWriter($this->filename, ',', '"', '\\', false, false, "\r\n");
        $writer->open();

        $writer->write(['john', 'doe', '1']);
        $writer->write(['john', 'doe', '2']);

        $writer->close();

        $expected = "john,doe,1\r\njohn,doe,2";

        $this->assertEquals($expected, trim(file_get_contents($this->filename)));
    }

    public function testEnclosureFormatingWithExcel()
    {
        $writer = new CsvWriter($this->filename, ',', '"', '\\', false);
        $writer->open();

        $writer->write(['john , ""2"', 'doe ', '1']);

        $writer->close();

        $expected = '"john , """"2""","doe ",1';

        $this->assertEquals($expected, trim(file_get_contents($this->filename)));
    }

    public function testWithHeaders()
    {
        $writer = new CsvWriter($this->filename, ',', '"', '\\', true);
        $writer->open();

        $writer->write(['name' => 'john , ""2"', 'surname' => 'doe ', 'year' => '2001']);

        $writer->close();

        $expected = 'name,surname,year'."\n".'"john , """"2""","doe ",2001';

        $this->assertEquals($expected, trim(file_get_contents($this->filename)));
    }

    public function testWithBom()
    {
        $writer = new CsvWriter($this->filename, ',', '"', '\\', true, true);
        $writer->open();

        $writer->write(['name' => 'Rémi , ""2"', 'surname' => 'doe ', 'year' => '2001']);

        $writer->close();

        $expected = \chr(0xEF).\chr(0xBB).\chr(0xBF).'name,surname,year'."\n".'"Rémi , """"2""","doe ",2001';
        $this->assertEquals($expected, trim(file_get_contents($this->filename)));
    }

    protected function getWriter()
    {
        return new CsvWriter('/tmp/whatever.csv');
    }
}
