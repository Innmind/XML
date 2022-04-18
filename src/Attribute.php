<?php
declare(strict_types = 1);

namespace Innmind\Xml;

use Innmind\Immutable\{
    Str,
    Maybe,
};

/**
 * @psalm-immutable
 */
class Attribute
{
    /** @var non-empty-string */
    private string $name;
    private string $value;

    /**
     * @param non-empty-string $name
     */
    private function __construct(string $name, string $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     */
    public static function of(string $name, string $value = ''): self
    {
        return new self($name, $value);
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

        return Maybe::just(new self($name, $value));
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

    public function toString(): string
    {
        $string = $this->name;

        if (!Str::of($this->value)->empty()) {
            $string .= \sprintf(
                '="%s"',
                $this->value,
            );
        }

        return $string;
    }
}
