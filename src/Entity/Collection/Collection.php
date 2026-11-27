<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Countable;
use Iterator;
use Paysera\CheckoutSdk\Exception\InvalidTypeException;

/**
 * @phpstan-consistent-constructor
 * @template ItemInterface
 * @implements Iterator<ItemInterface>
 */
abstract class Collection implements Iterator, Countable
{
    private int $position;

    private array $array;

    public function __construct(array $array = [])
    {
        $this->position = 0;
        $this->exchangeArray($array);
    }

    abstract public function getItemType(): string;

    abstract public function isCompatible(object $item): bool;

    public function count(): int
    {
        return count($this->array);
    }

    public function current(): ItemInterface
    {
        return $this->array[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Returns new collection instance with filtered items.
     * @param callable $filterFunction
     * @return Collection<ItemInterface>
     */
    public function filter(callable $filterFunction): Collection
    {
        $filteredArray = array_filter($this->array, $filterFunction);

        return new static(array_values($filteredArray));
    }

    public function exchangeArray(array $array): void
    {
        $isCompatible = array_reduce($array, fn ($carry, $item) => $carry && $this->isCompatible($item), true);

        if ($isCompatible === false) {
            throw new InvalidTypeException($this->getItemType());
        }

        $this->rewind();
        $this->array = $array;
    }

    /**
     * @param ItemInterface $value
     * @throws InvalidTypeException
     */
    public function append($value): void
    {
        if ($this->isCompatible($value) === false) {
            throw new InvalidTypeException($this->getItemType());
        }
        $this->array[] = $value;
    }

    /**
     * @return ItemInterface|null
     * @param null|int $index
     */
    public function get(?int $index = null)
    {
        return $this->array[$index ?? $this->position] ?? null;
    }
}
