<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;

/**
 * @template PaymentMethodCountry
 * @extends Collection<PaymentMethodCountry>
 *
 * @method PaymentMethodCountryCollection<PaymentMethodCountry> filter(callable $filterFunction)
 * @method void append(PaymentMethodCountry $value)
 * @method PaymentMethodCountry|null get(?int $index = null)
 */
class PaymentMethodCountryCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof PaymentMethodCountry;
    }

    public function current(): PaymentMethodCountry
    {
        return parent::current();
    }

    public function getItemType(): string
    {
        return PaymentMethodCountry::class;
    }
}
