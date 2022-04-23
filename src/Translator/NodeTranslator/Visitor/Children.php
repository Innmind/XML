<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\{
    Translator\Translator,
    Node,
};
use Innmind\Immutable\{
    Sequence,
    Maybe,
};

/**
 * @psalm-immutable
 */
final class Children
{
    private Translator $translate;

    private function __construct(Translator $translate)
    {
        $this->translate = $translate;
    }

    /**
     * @return Maybe<Sequence<Node>>
     */
    public function __invoke(\DOMNode $node): Maybe
    {
        /** @var Maybe<Sequence<Node>> */
        $children = Maybe::just(Sequence::of());

        foreach ($node->childNodes as $child) {
            $children = $children->flatMap(
                fn($children) => ($this->translate)($child)->map(
                    static fn($node) => ($children)($node),
                ),
            );
        }

        return $children;
    }

    /**
     * @psalm-pure
     */
    public static function of(Translator $translate): self
    {
        return new self($translate);
    }
}
