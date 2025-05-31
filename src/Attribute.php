<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Immutable\Maybe;

/**
 * @psalm-immutable
 */
final class Attribute
{
    /**
     * @param ?non-empty-string $namespace
     * @param non-empty-string $name
     */
    private function __construct(
        private ?string $namespace,
        private string $name,
        private string $value = '',
    ) {
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     */
    public static function of(string $name, string $value = ''): self
    {
        return new self(null, $name, $value);
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $namespace
     * @param non-empty-string $name
     */
    public static function namespaced(
        string $namespace,
        string $name,
        string $value = '',
    ): self {
        return new self($namespace, $name, $value);
    }

    /**
     * @return Maybe<self>
     */
    public static function maybe(string $name, string $value = ''): Maybe
    {
        if ($name === '') {
            /** @var Maybe<self> */
            return Maybe::nothing();
        }

        return Maybe::just(new self(null, $name, $value));
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    private function render(\XMLWriter $writer): void
    {
        if (\is_null($this->namespace)) {
            /** @psalm-suppress ImpureMethodCall */
            $writer->writeAttribute($this->name, $this->value);
        } else {
            /** @psalm-suppress ImpureMethodCall */
            $writer->writeAttributeNs(
                $this->namespace,
                $this->name,
                null,
                $this->value,
            );
        }
    }
}
