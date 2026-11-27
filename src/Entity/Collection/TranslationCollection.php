<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\Translation;

/**
 * @template Translation
 * @extends Collection<Translation>
 *
 * @method TranslationCollection<Translation> filter(callable $filterFunction)
 * @method void append(Translation $value)
 * @method Translation|null get(?int $index = null)
 */
class TranslationCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof Translation;
    }

    public function current(): Translation
    {
        return parent::current();
    }

    public function getItemType(): string
    {
        return Translation::class;
    }
}
