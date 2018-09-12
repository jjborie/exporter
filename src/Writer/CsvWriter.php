<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Exporter\Writer;

use Exporter\Exception\InvalidDataFormatException;
use League\Csv\Writer;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class CsvWriter implements TypedWriterInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $enclosure;

    /**
     * @var string
     */
    protected $escape;

    /**
     * @var resource
     */
    protected $file;

    /**
     * @var bool
     */
    protected $showHeaders;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var bool
     */
    protected $withBom;

    /**
     * @var string
     */
    private $terminate;

    /**
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param bool   $showHeaders
     * @param bool   $withBom
     * @param string $terminate
     */
    public function __construct(
        $filename,
        $delimiter = ',',
        $enclosure = '"',
        $escape = '\\',
        $showHeaders = true,
        $withBom = false,
        $terminate = "\n"
    ) {
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
        $this->showHeaders = $showHeaders;
        $this->terminate = $terminate;
        $this->position = 0;
        $this->withBom = $withBom;

        // Warning: If your CSV document was created or is read on a Macintosh computer,
        // add the following lines before using the library to help PHP detect line ending.
        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        if (is_file($filename)) {
            throw new \RuntimeException(sprintf('The file %s already exist', $filename));
        }
    }

    /**
     * {@inheritdoc}
     */
    final public function getDefaultMimeType()
    {
        return 'text/csv';
    }

    /**
     * {@inheritdoc}
     */
    final public function getFormat()
    {
        return 'csv';
    }

    /**
     * {@inheritdoc}
     */
    public function open()
    {
        $this->file = Writer::createFromFileObject(new SplTempFileObject());
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->file->output($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $data)
    {
        if (0 == $this->position && $this->showHeaders) {
            $this->addHeaders($data);

            ++$this->position;
        }

//        $result = @fputcsv($this->file, $data, $this->delimiter, $this->enclosure, $this->escape);
        $csv->insertOne($data);

        if (!$result) {
            throw new InvalidDataFormatException();
        }

        ++$this->position;
    }

    /**
     * @param array $data
     */
    protected function addHeaders(array $data)
    {
        $headers = [];
        foreach ($data as $header => $value) {
            $headers[] = $header;
        }

        $csv->insertOne($headers);
    }
}
