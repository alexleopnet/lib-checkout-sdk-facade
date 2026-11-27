<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Collection;

use Paysera\CheckoutSdk\Validator\RequestValidatorInterface;

/**
 * @template RequestValidatorInterface
 * @extends Collection<RequestValidatorInterface>
 *
 * @method RequestValidatorCollection<RequestValidatorInterface> filter(callable $filterFunction)
 * @method void append(RequestValidatorInterface $value)
 * @method RequestValidatorInterface|null get(?int $index = null)
 */
class RequestValidatorCollection extends Collection
{
    public function isCompatible(object $item): bool
    {
        return $item instanceof RequestValidatorInterface;
    }

    public function current(): RequestValidatorInterface
    {
        return parent::current();
    }

    public function getItemType(): string
    {
        return RequestValidatorInterface::class;
    }
}
