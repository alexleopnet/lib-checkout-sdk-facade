<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;

/**
 * @template PaymentMethodGroup
 * @extends Collection<PaymentMethodGroup>
 *
 * @method PaymentMethodGroupCollection<PaymentMethodGroup> filter(callable $filterFunction)
 * @method void append(PaymentMethodGroup $value)
 * @method PaymentMethodGroup|null get(?int $index = null)
 */
class PaymentMethodGroupCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof PaymentMethodGroup;
    }

    public function current(): PaymentMethodGroup
    {
        return parent::current();
    }

    public function getItemType(): string
    {
        return PaymentMethodGroup::class;
    }
}
