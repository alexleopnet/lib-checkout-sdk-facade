<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\Request\PaymentMethodsRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Entity\PaymentCallbackValidationResponse;
use Paysera\CheckoutSdk\Entity\PaymentRedirectResponse;
use Paysera\CheckoutSdk\Exception\BaseException;
use Paysera\CheckoutSdk\Exception\ProviderException;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentCallbackValidationRequestNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentMethodCountryAdapter;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentRedirectRequestNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentValidationResponseNormalizer;
use Paysera\CheckoutSdk\Provider\WebToPay\WebToPayProvider;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use WebToPay;
use WebToPay_Factory;
use WebToPay_UrlBuilder;
use WebToPay_PaymentMethodCountry;
use WebToPay_PaymentMethodList;
use WebToPay_RequestBuilder;
use WebToPayException;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class WebToPayProviderTest extends AbstractCase
{
    /** @var PaymentMethodCountryAdapter|null|m\MockInterface  */
    protected ?PaymentMethodCountryAdapter $paymentMethodCountryAdapterMock = null;

    /** @var PaymentValidationResponseNormalizer|null|m\MockInterface */
    protected ?PaymentValidationResponseNormalizer $paymentValidationResponseNormalizer = null;

    protected ?WebToPayProvider $webToPayProvider = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->paymentMethodCountryAdapterMock = m::mock(PaymentMethodCountryAdapter::class);
        $this->paymentValidationResponseNormalizer = m::mock(PaymentValidationResponseNormalizer::class);

        $this->webToPayProvider = new WebToPayProvider(
            $this->paymentMethodCountryAdapterMock,
            $this->paymentValidationResponseNormalizer,
            new PaymentRedirectRequestNormalizer(),
            new PaymentCallbackValidationRequestNormalizer()
        );
    }

    public function testGetPaymentMethodCountries(): void
    {
        $methodRequest = new PaymentMethodsRequest(
            1,
            100,
            'USD'
        );

        $providerMethodCountryMock = m::mock(WebToPay_PaymentMethodCountry::class);
        $providerMethodListMock = m::mock(WebToPay_PaymentMethodList::class);
        $providerMethodListMock->expects()
            ->getCountries()
            ->andReturn([$providerMethodCountryMock]);

        m::mock('overload:'. WebToPay::class)
            ->expects()
            ->getPaymentMethodList(
                $methodRequest->getProjectId(),
                $methodRequest->getAmount(),
                $methodRequest->getCurrency()
            )
            ->andReturn($providerMethodListMock);

        $paymentMethodCountry = m::mock(PaymentMethodCountry::class);
        $this->paymentMethodCountryAdapterMock->expects()
            ->convert($providerMethodCountryMock)
            ->andReturn($paymentMethodCountry);

        $collection = $this->webToPayProvider->getPaymentMethods($methodRequest);

        $collection->rewind();
        $this->assertEquals(
            $paymentMethodCountry,
            $collection->get(),
            'The provider must return countries collection.'
        );
    }

    public function testGetPaymentRedirect(): void
    {
        [$redirectRequest, $providerData] = $this->getPaymentRedirectRequestAndProviderData();

        $builder = m::mock(WebToPay_RequestBuilder::class)
            ->shouldReceive('buildRequest')
            ->with($providerData)
            ->andReturn(['data' => 'test_data'])
            ->getMock();

        $factory = m::mock('overload:'. WebToPay_Factory::class);

        $factory->shouldReceive('getRequestBuilder')
            ->andReturn($builder);

        $urlBuilder = m::mock(WebToPay_UrlBuilder::class)
            ->shouldReceive('buildForRequest')
            ->andReturn('http://example.paysera.test')
            ->getMock();

        $factory->shouldReceive('getUrlBuilder')
            ->andReturn($urlBuilder);

        $response = $this->webToPayProvider->getPaymentRedirect($redirectRequest);

        $this->assertInstanceOf(PaymentRedirectResponse::class, $response);
        $this->assertEquals('http://example.paysera.test', $response->getRedirectUrl());
        $this->assertEquals('test_data', $response->getData());
    }

    public function testGetPaymentCallbackValidatedData(): void
    {
        [$validationRequest, $providerData] = $this->getPaymentValidationRequestAndProviderData();

        m::mock('overload:'. WebToPay::class)
            ->expects()
            ->validateAndParseData(
                $providerData,
                $validationRequest->getProjectId(),
                $validationRequest->getProjectPassword()
            )
            ->andReturn(['some data here...']);

        $validationResponseMock = m::mock(PaymentCallbackValidationResponse::class);
        $this->paymentValidationResponseNormalizer->expects()
            ->denormalize(['some data here...'])
            ->andReturn($validationResponseMock);

        $validateResponse = $this->webToPayProvider->getPaymentCallbackValidatedData($validationRequest);

        $this->assertEquals(
            $validationResponseMock,
            $validateResponse,
            'The provider must return validation response.'
        );
    }

    public function testGetPaymentRedirectProviderExceptions(): void
    {
        m::mock('overload:'. WebToPay_Factory::class)
            ->shouldReceive('getRequestBuilder')
            ->once()
            ->withAnyArgs()
            ->andThrow(new WebToPayException('Some troubles.'));

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessageMatches('/Provider thrown exception in .*/');
        $this->expectExceptionCode(BaseException::E_PROVIDER_ISSUE);

        $request = new PaymentRedirectRequest(
            1,
            'pass',
            'acceptUrl',
            'cancelUrl',
            'callbackUrl',
            new Order(1, 100, 'USD')
        );

        $this->webToPayProvider->getPaymentRedirect($request);
    }

    /**
     * @dataProvider exceptionsProvider
     */
    public function testMethodsExceptions(string $method, object $request, string $providerMethod): void
    {
        m::mock('overload:'. WebToPay::class)
            ->shouldReceive($providerMethod)
            ->once()
            ->withAnyArgs()
            ->andThrow(new WebToPayException('Some troubles.'));

        $this->expectException(ProviderException::class);
        $this->expectExceptionMessageMatches('/Provider thrown exception in .*/');
        $this->expectExceptionCode(BaseException::E_PROVIDER_ISSUE);

        $this->webToPayProvider->{$method}($request);
    }

    public function exceptionsProvider(): array
    {
        return [
            [
                'WebToPayProvider method' => 'getPaymentMethods',
                'Request argument' => new PaymentMethodsRequest(
                    1,
                    100,
                    'USD'
                ),
                'WebToPay static method' => 'getPaymentMethodList',
            ],
            [
                'WebToPayProvider method' => 'getPaymentCallbackValidatedData',
                'Request argument' => new PaymentCallbackValidationRequest(
                    1,
                    'pass',
                    'test data'
                ),
                'WebToPay static method' => 'validateAndParseData',
            ],
        ];
    }

    /**
     * @return array{0: PaymentRedirectRequest, 1: array}
     */
    protected function getPaymentRedirectRequestAndProviderData(): array
    {
        $orderRequest = (new Order(
            1,
            100,
            'USD'
        ))->setPayerFirstName('John')
            ->setPayerLastName('Doe')
            ->setPayerEmail('john.doe@paysera.net')
            ->setPayerStreet('Sun str. 1')
            ->setPayerCity('London')
            ->setPayerZip('100')
            ->setPayerCountryCode('gb');
        $redirectRequest = new PaymentRedirectRequest(
            1,
            'pass',
            'acceptUrl',
            'cancelUrl',
            'callbackUrl',
            $orderRequest
        );
        $redirectRequest->setPayment('card')
            ->setTest(true);

        $providerData = [
            'orderid' => 1,
            'amount' => 100,
            'currency' => 'USD',
            'accepturl' => 'acceptUrl',
            'cancelurl' => 'cancelUrl',
            'callbackurl' => 'callbackUrl',
            'version' => WebToPay::VERSION,
            'payment' => 'card',
            'p_firstname' => 'John',
            'p_lastname' => 'Doe',
            'p_email' => 'john.doe@paysera.net',
            'p_street' => 'Sun str. 1',
            'p_city' => 'London',
            'p_zip' => '100',
            'p_countrycode' => 'gb',
            'test' => 1,
            'buyer_consent' => 0,
            'php_version' => phpversion(),
        ];

        return [$redirectRequest, $providerData];
    }

    /**
     * @return array{0: PaymentCallbackValidationRequest, 1: array}
     */
    protected function getPaymentValidationRequestAndProviderData(): array
    {
        $validationRequest = (new PaymentCallbackValidationRequest(
            1,
            'pass',
            'test data'
        ))
            ->setSs1('test ss1')
            ->setSs2('test ss2')
            ->setSs3('test ss3')
        ;

        $providerData = [
            'data' => 'test data',
            'ss1' => 'test ss1',
            'ss2' => 'test ss2',
            'ss3' => 'test ss3',
        ];

        return [$validationRequest, $providerData];
    }
}
