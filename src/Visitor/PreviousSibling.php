<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\Node;
use Innmind\Immutable\{
    Maybe,
    Sequence,
    Pair,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 */
final class PreviousSibling
{
    private Node $node;

    private function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return Maybe<Node>
     */
    public function __invoke(Node $tree): Maybe
    {
        return ParentNode::of($this->node)($tree)
            ->toSequence()
            ->flatMap(static fn($parent) => $parent->children())
            ->aggregate(static function($a, $b) {
                if ($a instanceof Pair) {
                    return Sequence::of(new Pair(
                        $a->value(),
                        $b,
                    ));
                }

                return Sequence::of(new Pair($a, $b));
            })
            ->keep(Instance::of(Pair::class))
            ->find(fn($pair) => $pair->value() === $this->node)
            ->map(static fn($pair): mixed => $pair->key())
            ->keep(Instance::of(Node::class));
    }

    /**
     * @psalm-pure
     */
    public static function of(Node $node): self
    {
        return new self($node);
    }
}
