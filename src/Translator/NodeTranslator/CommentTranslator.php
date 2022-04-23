<?php
declare(strict_types = 1);

namespace Innmind\Xml\Translator\NodeTranslator;

use Innmind\Xml\{
    Translator\NodeTranslator,
    Translator\Translator,
    Node,
    Node\Comment,
};
use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class CommentTranslator implements NodeTranslator
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
            ->filter(static fn($node) => $node instanceof \DOMComment)
            ->map(static fn(\DOMComment $node) => Comment::of($node->data));
    }

    /**
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }
}
