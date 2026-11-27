<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

/**
 * @link https://developers.paysera.com/en/checkout/integrations/integration-callback
 */
class PaymentCallbackValidationResponse
{
    /**
     * Unique project number. Only activated projects can accept payments.
     */
    private int $projectId;

    private Order $order;
    private ?Refund $refund;

    /**
     * Payment status:
     *     0 - Payment has not been executed
     *     1 - Payment successful
     *     2 - Payment order accepted, but not yet executed
     *     3 - Additional payment information
     *     4 - Payment was executed, but confirmation about received funds in bank won't be sent.
     *     5 - Payment was refunded
     */
    private int $status;

    /**
     * It is possible to indicate the user language (ISO 639-2/B: LIT, RUS, ENG, etc.).
     * If Paysera does not support the selected language, the system will automatically choose a language
     * according to the IP address or ENG language by default.
     */
    private ?string $language = null;

    /**
     * Payment type.
     * If provided, the payment will be made by the specified method (for example by using the specified bank).
     * If not specified, the payer will be immediately provided with the payment types to choose from.
     * You can get payment types in real time by using this library.
     */
    private ?string $payment = null;

    /**
     * Payer's country (LT, EE, LV, GB, PL, DE).
     * All possible types of payment in that country are immediately indicated to the payer, after selecting a country.
     */
    private ?string $country = null;

    private ?string $originalPaymentText = null;

    /**
     * Payment purpose visible when making the payment.
     */
    private ?string $paymentText = null;

    /**
     * Payer's name received from the payment system. Sent only if the payment system provides such.
     */
    private ?string $name = null;

    /**
     * Payer's surname received from the payment system. Sent only if the payment system provides such.
     */
    private ?string $surname = null;

    /**
     * The parameter, which allows to test the connection.
     * The payment is not executed, but the result is returned immediately, as if the payment has been made.
     */
    private bool $test = false;

    /**
     * Country of the payment method.
     * If the payment method is available in more than one country (international) – the parameter is not sent.
     * The country is provided in the two-character (ISO 3166-1 alpha-2) format, e.g.: LT, PL, RU, EE.
     */
    private ?string $paymentCountry = null;

    /**
     * Country of the payer established by the IP address of the payer.
     * The country is provided in two-character (ISO 3166-1 alpha-2) format, e.g.: LT, PL, RU, EE.
     */
    private ?string $payerIpCountry = null;

    /**
     * Country of the payer established by the country of the payment method,
     * and if the payment method is international – by the IP address of the payer.
     * The country is provided in the two-character (ISO 3166-1 alpha-2) format, e.g.: LT, PL, RU, EE.
     */
    private ?string $payerCountry = null;

    /**
     * Amount of the transfer in cents. It can differ, if it was converted to another currency.
     */
    private ?int $paymentAmount = null;

    /**
     * The transferred payment currency (i.e. USD, EUR, etc.).
     * It can differ from the one you requested, if the currency could not be accepted by the selected payment method.
     */
    private ?string $paymentCurrency = null;

    /**
     * A version number of Paysera system specification (API).
     */
    private ?string $version = null;

    /**
     * It is a request number, which we receive when the user presses on the logo of the bank.
     * We transfer this request number to the link provided in the "callbackurl" field.
     */
    private ?string $requestId = null;

    /**
     * Account number from which payment has been made.
     */
    private ?string $account = null;

    private ?string $type = null;

    public function __construct(int $projectId, Order $order, int $status)
    {
        $this->projectId = $projectId;
        $this->order = $order;
        $this->status = $status;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getOriginalPaymentText(): ?string
    {
        return $this->originalPaymentText;
    }

    public function setOriginalPaymentText(?string $originalPaymentText): self
    {
        $this->originalPaymentText = $originalPaymentText;

        return $this;
    }

    public function getPaymentText(): ?string
    {
        return $this->paymentText;
    }

    public function setPaymentText(?string $paymentText): self
    {
        $this->paymentText = $paymentText;

        return $this;
    }

    public function isTest(): bool
    {
        return $this->test;
    }

    public function setTest(?bool $test): self
    {
        $this->test = (bool) $test;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function setRequestId(?string $requestId): self
    {
        $this->requestId = $requestId;

        return $this;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(?string $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPaymentCountry(): ?string
    {
        return $this->paymentCountry;
    }

    public function setPaymentCountry(?string $paymentCountry): self
    {
        $this->paymentCountry = $paymentCountry;

        return $this;
    }

    public function getPayerIpCountry(): ?string
    {
        return $this->payerIpCountry;
    }

    public function setPayerIpCountry(?string $payerIpCountry): self
    {
        $this->payerIpCountry = $payerIpCountry;

        return $this;
    }

    public function getPayerCountry(): ?string
    {
        return $this->payerCountry;
    }

    public function setPayerCountry(?string $payerCountry): self
    {
        $this->payerCountry = $payerCountry;

        return $this;
    }

    public function getPaymentAmount(): ?int
    {
        return $this->paymentAmount;
    }

    public function setPaymentAmount(?int $paymentAmount): self
    {
        $this->paymentAmount = $paymentAmount;

        return $this;
    }

    public function getPaymentCurrency(): ?string
    {
        return $this->paymentCurrency;
    }

    public function setPaymentCurrency(?string $paymentCurrency): self
    {
        $this->paymentCurrency = $paymentCurrency;

        return $this;
    }

    public function getRefund(): ?Refund
    {
        return $this->refund;
    }

    public function setRefund(?Refund $refund): self
    {
        $this->refund = $refund;

        return $this;
    }
}
