<?php
declare(strict_types = 1);

namespace Innmind\Xml\Visitor;

use Innmind\Xml\{
    Node,
    Element,
    Element\Custom,
    Document,
};
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
    private function __construct(
        private Node|Element|Custom $node,
    ) {
    }

    /**
     * @return Maybe<Node|Element|Custom>
     */
    #[\NoDiscard]
    public function __invoke(Document|Node|Element|Custom $tree): Maybe
    {
        return ParentNode::of($this->node)($tree)
            ->toSequence()
            ->flatMap(static fn($parent) => match (true) {
                $parent instanceof Custom => $parent->normalize()->children(),
                default => $parent->children(),
            })
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
            ->keep(
                Instance::of(Node::class)->or(
                    Instance::of(Element::class)
                        ->or(Instance::of(Custom::class)),
                ),
            );
    }

    /**
     * @psalm-pure
     */
    #[\NoDiscard]
    public static function of(Node|Element|Custom $node): self
    {
        return new self($node);
    }
}
