<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Node\CharacterData,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class CharacterDataTranslator implements NodeTranslator
{
    private function __construct()
    {
    }

    public function __invoke(\DOMNode $node, Translator $translate): Maybe
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @var Maybe<Node>
         */
        return Maybe::just($node)
            ->filter(static fn($node) => $node instanceof \DOMCharacterData)
            ->map(static fn(\DOMCharacterData $node) => CharacterData::of($node->data));
    }

    /**
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }
}
