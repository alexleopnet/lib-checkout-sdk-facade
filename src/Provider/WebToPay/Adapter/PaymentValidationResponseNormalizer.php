<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Entity\Refund;
use Paysera\CheckoutSdk\Util\TypeConverter;

class PaymentValidationResponseNormalizer
{
    protected TypeConverter $typeConverter;

    public function __construct(TypeConverter $typeConverter)
    {
        $this->typeConverter = $typeConverter;
    }

    /**
     * @param array<string, mixed> $providerResponse
     */
    public function denormalize(array $providerResponse): PaymentCallbackValidationResponse
    {
        $order = $this->getOrderFromProviderResponse($providerResponse);

        $paymentValidationResponse = new PaymentCallbackValidationResponse(
            $this->getProviderProperty('projectid', $providerResponse, TypeConverter::INT),
            $order,
            $this->getProviderProperty('status', $providerResponse, TypeConverter::INT),
        );

        return $paymentValidationResponse
            ->setPayment($this->getProviderProperty('payment', $providerResponse))
            ->setPaymentText($this->getProviderProperty('paytext', $providerResponse))
            ->setOriginalPaymentText($this->getProviderProperty('original_paytext', $providerResponse))
            ->setVersion($this->getProviderProperty('version', $providerResponse))
            ->setRequestId($this->getProviderProperty('requestid', $providerResponse))
            ->setAccount($this->getProviderProperty('account', $providerResponse))
            ->setType($this->getProviderProperty('type', $providerResponse))
            ->setTest($this->getProviderProperty('test', $providerResponse, TypeConverter::BOOL))
            ->setName($this->getProviderProperty('name', $providerResponse))
            ->setSurname($this->getProviderProperty('surname', $providerResponse))
            ->setLanguage($this->getProviderProperty('lang', $providerResponse))
            ->setCountry($this->getProviderProperty('country', $providerResponse))
            ->setPaymentCountry($this->getProviderProperty('payment_country', $providerResponse))
            ->setPayerIpCountry($this->getProviderProperty('payer_ip_country', $providerResponse))
            ->setPayerCountry($this->getProviderProperty('payer_country', $providerResponse))
            ->setPaymentAmount($this->getProviderProperty('payamount', $providerResponse, TypeConverter::INT))
            ->setPaymentCurrency($this->getProviderProperty('paycurrency', $providerResponse))
            ->setRefund($this->getRefundFromProviderResponse($providerResponse))
        ;
    }

    /**
     * @param array<string, mixed> $providerResponse
     */
    protected function getRefundFromProviderResponse(array $providerResponse): ?Refund
    {
        if (!isset($providerResponse['refund_timestamp'])) {
            return null;
        }
        return new Refund(
            $this->getProviderProperty('refund_amount', $providerResponse),
            $this->getProviderProperty('refund_currency', $providerResponse),
            $this->getProviderProperty('refund_commission_amount', $providerResponse),
            $this->getProviderProperty('refund_commission_currency', $providerResponse),
            $this->getProviderProperty('refund_timestamp', $providerResponse, TypeConverter::INT),
        );
    }

    /**
     * @param array<string, mixed> $providerResponse
     */
    protected function getOrderFromProviderResponse(array $providerResponse): Order
    {
        $order = new Order(
            $this->getProviderProperty('orderid', $providerResponse, TypeConverter::INT),
            $this->getProviderProperty('amount', $providerResponse, TypeConverter::INT),
            $this->getProviderProperty('currency', $providerResponse),
        );

        return $order->setPayerFirstName($this->getProviderProperty('p_firstname', $providerResponse))
            ->setPayerLastName($this->getProviderProperty('p_lastname', $providerResponse))
            ->setPayerEmail($this->getProviderProperty('p_email', $providerResponse))
            ->setPayerStreet($this->getProviderProperty('p_street', $providerResponse))
            ->setPayerCity($this->getProviderProperty('p_city', $providerResponse))
            ->setPayerZip($this->getProviderProperty('p_zip', $providerResponse))
            ->setPayerState($this->getProviderProperty('p_state', $providerResponse))
            ->setPayerCountryCode($this->getProviderProperty('p_countrycode', $providerResponse))
        ;
    }

    /**
     * @param array<string, mixed> $providerResponse
     * @return mixed
     */
    protected function getProviderProperty(
        string $propertyName,
        array $providerResponse,
        int $convertToType = TypeConverter::DEFAULT
    ) {
        if (isset($providerResponse[$propertyName]) === false) {
            return null;
        }

        return $this->typeConverter->convert($providerResponse[$propertyName] ?? null, $convertToType);
    }
}
