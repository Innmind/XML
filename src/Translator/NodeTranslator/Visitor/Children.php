<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator\Visitor;

use Innmind\Xml\{
    Translator\Translator,
    Node,
    Element,
};
use Innmind\Immutable\{
    Sequence,
    Maybe,
    Predicate\Instance,
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
     * @return Maybe<Sequence<Node|Element>>
     */
    public function __invoke(\DOMNode $node): Maybe
    {
        /** @var Maybe<Sequence<Node|Element>> */
        $children = Maybe::just(Sequence::of());

        /**
         * @psalm-suppress ImpureMethodCall
         * @var \DOMNode $child
         */
        foreach ($node->childNodes as $child) {
            $children = $children->flatMap(
                fn($children) => ($this->translate)($child)
                    ->keep(
                        Instance::of(Node::class)->or(
                            Instance::of(Element::class),
                        ),
                    )
                    ->map($children),
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
