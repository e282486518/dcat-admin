<?php

namespace Dcat\Admin\Support;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\Output;

class StringOutput extends Output
{
    public $output = '';

    public function __construct(int $verbosity = self::VERBOSITY_NORMAL, bool $decorated = false, ?OutputFormatterInterface $formatter = null)
    {
        $formatter = $formatter ?: new OutputFormatter();

        parent::__construct($verbosity, $decorated, $formatter);
    }

    public function clear()
    {
        $this->output = '';
    }

    protected function doWrite(string $message, bool $newline): void
    {
        $this->output .= $message.($newline ? "\n" : '');
    }

    public function getContent()
    {
        return trim($this->output);
    }
}
