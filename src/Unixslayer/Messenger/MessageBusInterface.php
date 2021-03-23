<?php

declare(strict_types=1);

namespace Unixslayer\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

interface MessageBusInterface
{
    /** @psalm-suppress MissingReturnType */
    public function dispatch(object $message, StampInterface ...$stamps);
}
