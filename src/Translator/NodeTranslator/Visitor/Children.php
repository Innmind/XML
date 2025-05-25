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
        /** @var Sequence<Node|Element> */
        $translated = Sequence::of();

        /**
         * @psalm-suppress ImpureFunctionCall
         * @psalm-suppress ImpureMethodCall
         */
        return Sequence::of(...\array_values(\iterator_to_array($node->childNodes)))
            ->keep(Instance::of(\DOMNode::class))
            ->sink($translated)
            ->maybe(
                fn($translated, $child) => ($this->translate)($child)
                    ->keep(
                        Instance::of(Node::class)->or(
                            Instance::of(Element::class),
                        ),
                    )
                    ->map($translated),
            );
    }

    /**
     * @psalm-pure
     */
    public static function of(Translator $translate): self
    {
        return new self($translate);
    }
}
