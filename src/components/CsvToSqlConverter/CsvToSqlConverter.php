<?php

declare(strict_types=1);

namespace Sanweb\Taskforce\components\CsvToSqlConverter;

use Sanweb\Taskforce\exception\FileException;
use SplFileObject;

final class CsvToSqlConverter
{
    private SplFileObject $reader;
    private array $options = [
        //'encoding' => 'UTF-8',
        'delimiter' => ',',
        'enclosure' => '"',
        'escape' => '',
    ];

    public function __construct(
        string $file,
        array $options = []
    ) {
        $this->options = array_replace($this->options, $options);

        $this->validateFile($file);

        $this->reader = $this->createReader($file);
    }

    private function validateFile(string $file): void
    {
        if (!is_file($file)) {
            throw new FileException("File $file not found.");
        }

        if (!is_readable($file)) {
            throw new FileException("File $file is not readable.");
        }

        if (!$this->isUtf8($file)) {
            throw new FileException("File $file must be encoded in UTF-8");
        }
    }

    private function createReader(string $file): SplFileObject
    {
        $reader = new SplFileObject($file, 'rb');

        $reader->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        $reader->setCsvControl(
            $this->options['delimiter'],
            $this->options['enclosure'],
            $this->options['escape'],
        );

        return $reader;
    }

    private function isUtf8(string $file): bool
    {
        $reader = new SplFileObject($file, 'rb');

        foreach ($reader as $line) {
            if (!mb_check_encoding($line, 'UTF-8')) {
                return false;
            }
        }

        return true;
    }

    public function read(): iterable
    {
        foreach ($this->reader as $row) {
            if (is_array($row)) {
                yield $row;
            }
        }
    }

    protected function convertToSql(array $data): string
    {
        $sql = '';

        return $sql;
    }

    protected function saveSqlFile(): bool
    {
        return true;
    }
}
