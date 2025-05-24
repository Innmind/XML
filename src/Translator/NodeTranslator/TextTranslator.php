<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
};
use Innmind\Immutable\{
    Maybe,
    Predicate\Instance,
};

/**
 * @psalm-immutable
 */
final class TextTranslator implements NodeTranslator
{
    private function __construct()
    {
    }

    #[\Override]
    public function __invoke(\DOMNode $node, Translator $translate): Maybe
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @var Maybe<Node>
         */
        return Maybe::just($node)
            ->keep(Instance::of(\DOMText::class))
            ->map(static fn(\DOMText $node) => Node::text($node->data));
    }

    /**
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }
}
