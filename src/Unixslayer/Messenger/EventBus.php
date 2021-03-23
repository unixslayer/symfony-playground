<?php

declare(strict_types=1);

namespace Unixslayer\Messenger;

use Assert\Assertion;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @codeCoverageIgnore
 */
final class EventBus implements EventBusInterface
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function dispatchAll(array $messages, StampInterface ...$stamps): void
    {
        Assertion::allIsObject($messages);

        foreach ($messages as $message) {
            $this->dispatch($message, ...$stamps);
        }
    }

    public function dispatch(object $message, StampInterface ...$stamps): void
    {
        if (!$message instanceof Envelope) {
            $message = Envelope::wrap($message)->with(...$stamps);
        }

        $this->eventBus->dispatch($message);
    }
}
