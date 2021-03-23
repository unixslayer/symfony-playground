<?php

declare(strict_types=1);

namespace Unixslayer\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

interface EventBusInterface extends MessageBusInterface
{
    /**
     * @param object[] $messages
     */
    public function dispatchAll(array $messages, StampInterface ...$stamps): void;
}
