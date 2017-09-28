<?php

namespace Anavel\Crud\Services\Export;

class Csv
{
    private $fp;
    private $headers = [];
    private $csv = '';

    public function fromArray(array $data)
    {
        if (empty($data)) {
            return '';
        }

        $this->open();

        foreach ($data as $line) {
            $this->headers($line);
        }

        $this->write($this->headers);

        $fill = array_fill_keys($this->headers, '');

        foreach ($data as $line) {
            $this->write(array_merge($fill, $this->line($line)));
        }

        $this->close();

        return $this;
    }

    public function __toString()
    {
        return $this->csv;
    }

    private function open()
    {
        // Use memory as file
        $this->fp = fopen('php://memory', 'w');

        // Add BOM to fix UTF-8 in Excel
        fwrite($this->fp, chr(0xEF).chr(0xBB).chr(0xBF));
    }

    private function write(array $data)
    {
        fputcsv($this->fp, $data);
    }

    private function close()
    {
        rewind($this->fp);

        $this->csv = stream_get_contents($this->fp);

        fclose($this->fp);
    }

    private function headers(array $data, $prefix = null)
    {
        $prefix = $prefix ? ($prefix.'.') : '';

        foreach ($data as $key => $value) {
            $key = $prefix.$key;

            if ($this->existsHeader($key)) {
                continue;
            }

            $isArray = is_array($value);

            if ($isArray && array_key_exists(0, $value)) {
                continue;
            }

            if ($isArray) {
                $this->headers($value, $key);
            } elseif ($this->isValidHeader($key)) {
                $this->headers[] = $key;
            }
        }
    }

    private function line(array $line, $prefix = null)
    {
        $data = [];
        $prefix = $prefix ? ($prefix.'.') : '';

        foreach ($line as $key => $value) {
            $key = $prefix.$key;
            $isArray = is_array($value);

            if ($isArray && array_key_exists(0, $value)) {
                continue;
            }

            if ($isArray) {
                $data = array_merge($data, $this->line($value, $key));
            } elseif ($this->existsHeader($key)) {
                $data[$key] = trim(str_replace(["\n", "\r"], '', $value));
            }
        }

        return $data;
    }

    private function isValidHeader($key)
    {
        return preg_match('/_id$/', $key) === 0;
    }

    private function existsHeader($key)
    {
        return in_array($key, $this->headers, true);
    }
}
