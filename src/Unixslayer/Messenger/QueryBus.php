<?php

declare(strict_types=1);

namespace Unixslayer\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @codeCoverageIgnore
 */
final class QueryBus implements QueryBusInterface
{
    private MessageBusInterface $queryBus;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * @param Envelope|object $message   The message or the message pre-wrapped in an envelope
     * @param StampInterface  ...$stamps
     *
     * @return mixed The handler returned value
     * @psalm-suppress UndefinedFunction
     */
    public function dispatch(object $message, StampInterface ...$stamps)
    {
        if (!$message instanceof Envelope) {
            $message = Envelope::wrap($message)->with(...$stamps);
        }

        $envelope = $this->queryBus->dispatch($message);
        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if ($handledStamps === []) {
            throw new LogicException(sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', get_debug_type($envelope->getMessage()), static::class, __FUNCTION__));
        }

        if (\count($handledStamps) > 1) {
            $handlers = implode(', ', array_map(static function (HandledStamp $stamp): string {
                return sprintf('"%s"', $stamp->getHandlerName());
            }, $handledStamps));

            throw new LogicException(sprintf(
                'Message of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.',
                get_debug_type($envelope->getMessage()),
                __CLASS__,
                __FUNCTION__,
                \count($handledStamps),
                $handlers
            ));
        }

        return $handledStamps[0]->getResult();
    }
}
