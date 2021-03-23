<?php

declare(strict_types=1);

namespace Unixslayer\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @codeCoverageIgnore
 */
final class CommandBus implements CommandBusInterface
{
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function dispatch(object $message, StampInterface ...$stamps): Envelope
    {
        if (!$message instanceof Envelope) {
            $message = Envelope::wrap($message)->with(...$stamps);
        }

        return $this->commandBus->dispatch($message);
    }
}
