<?php

namespace Amp\ByteStream\Base64;

use Amp\ByteStream\OutputStream;
use Amp\Future;

final class Base64EncodingOutputStream implements OutputStream
{
    /** @var OutputStream */
    private OutputStream $destination;

    /** @var string */
    private string $buffer = '';

    public function __construct(OutputStream $destination)
    {
        $this->destination = $destination;
    }

    public function write(string $data): Future
    {
        $this->buffer .= $data;

        $length = \strlen($this->buffer);
        $chunk = \base64_encode(\substr($this->buffer, 0, $length - $length % 3));
        $this->buffer = \substr($this->buffer, $length - $length % 3);

        return $this->destination->write($chunk);
    }

    public function end(string $finalData = ""): Future
    {
        $chunk = \base64_encode($this->buffer . $finalData);
        $this->buffer = '';

        return $this->destination->end($chunk);
    }
}
