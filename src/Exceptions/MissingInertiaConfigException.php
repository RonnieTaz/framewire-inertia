<?php

namespace Framewire\Inertia\Exceptions;

use InvalidArgumentException;

class MissingInertiaConfigException extends InvalidArgumentException
{
    public static function fromMessage(string $message): self
    {
        return new self($message);
    }
}
