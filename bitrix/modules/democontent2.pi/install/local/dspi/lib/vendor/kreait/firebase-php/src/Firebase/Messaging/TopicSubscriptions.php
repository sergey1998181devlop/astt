<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use Countable;
use Generator;
use IteratorAggregate;

final class TopicSubscriptions implements Countable, IteratorAggregate
{
    /** @var TopicSubscription[] */
    private $subscriptions;

    public function __construct(TopicSubscription ...$subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    public function filter(callable $filter): self
    {
        return new self(...\array_filter($this->subscriptions, $filter));
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Generator|TopicSubscription[]
     */
    public function getIterator()
    {
        yield from $this->subscriptions;
    }

    public function count()
    {
        return \count($this->subscriptions);
    }
}
