<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Model\CheckoutConfigProvider;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CheckoutConfigProviderTest extends TestCase
{
    /** @var CheckoutConfigProvider */
    private $checkoutConfigProvider;

    /** @var ObjectManager|MockObject */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
    }

    private function getCheckoutSessionMock(): CheckoutSession
    {
        $quoteId = '45';
        $customerEmail = 'john@doe.com';

        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods([
                'getId',
                'getCustomerEmail'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);

        $quoteMock
            ->expects($this->once())
            ->method('getCustomerEmail')
            ->willReturn($customerEmail);

        $checkoutSession = $this->createMock(CheckoutSession::class);
        $checkoutSession
            ->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        return $checkoutSession;
    }

    private function getGenerateAccessTokenMock(): GenerateAccessToken
    {
        $accessToken = 'ACCESS_TOKEN_RANDOM_STRING';

        $generateAccessToken = $this->createMock(GenerateAccessToken::class);
        $generateAccessToken
            ->expects($this->once())
            ->method('execute')
            ->willReturn($accessToken);

        return $generateAccessToken;
    }

    private function getLoggerMock(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }

    private function getRefIdBuilderMock(): RefIdBuilderInterface
    {
        $refId = 'REF_ID_RANDOM_STRING';
        $clientId = 'PYW';
        $accessToken = 'ACCESS_TOKEN_RANDOM_STRING';
        $quoteId = '45';
        $customerEmail = 'john@doe.com';
        $sandboxMode = true;

        $refIdBuilder = $this->createMock(RefIdBuilderInterface::class);
        $refIdBuilder
            ->expects($this->once())
            ->method('buildRefId')
            ->with(
                $clientId,
                $accessToken,
                $clientId,
                time(),
                $quoteId,
                $customerEmail,
                $sandboxMode
            )
            ->willReturn($refId);

        return $refIdBuilder;
    }

    private function getSerializerMock(): SerializerInterface
    {
        return $this->createMock(SerializerInterface::class);
    }

    private function getScopeConfigMock(): ConfigInterface
    {
        $environment = 'sandbox';
        $clientId = 'PYW';
        $paymentSdkApiEndpoint =
            'https://checkout.uat.telluride.shopyourway.com/tell/api/checkout/v1/orderpaymentconfirm';

        $scopeConfig = $this->createMock(ConfigInterface::class);
        $scopeConfig
            ->expects($this->exactly(2))
            ->method('getEnvironment')
            ->willReturn($environment);

        $scopeConfig
            ->expects($this->once())
            ->method('getClientId')
            ->willReturn($clientId);

        $scopeConfig
            ->expects($this->once())
            ->method('isDebugMode')
            ->willReturn(false);

        $scopeConfig
            ->expects($this->once())
            ->method('getPaymentSdkApiEndpoint')
            ->willReturn($paymentSdkApiEndpoint);

        $scopeConfig
            ->expects($this->once())
            ->method('isPayYourWayEnabled')
            ->willReturn(true);

        return $scopeConfig;
    }

    private function getStoreManagerMock(): StoreManagerInterface
    {
        $currency_code = 'usd';

        $storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->setMethods([
                'getStore',
                'getCurrentCurrency',
                'getCode'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $storeManager
            ->expects($this->once())
            ->method('getStore')
            ->willReturnSelf();

        $storeManager
            ->expects($this->once())
            ->method('getCurrentCurrency')
            ->willReturnSelf();

        $storeManager
            ->expects($this->once())
            ->method('getCode')
            ->willReturn($currency_code);

        return $storeManager;
    }

    private function getCheckoutConfigProviderModelObject(
        ConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        GenerateAccessToken $generateAccessToken,
        RefIdBuilderInterface $refIdBuilder,
        CheckoutSession $checkoutSession,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ): void {
        $this->checkoutConfigProvider = $this->objectManager->getObject(
            CheckoutConfigProvider::class,
            [
                'scopeConfig' => $scopeConfig,
                'storeManager' => $storeManager,
                'generateAccessToken' => $generateAccessToken,
                'refIdBuilder' => $refIdBuilder,
                'checkoutSession' => $checkoutSession,
                'serializer' => $serializer,
                'logger' => $logger
            ]
        );
    }

    public function testGetConfig()
    {
        $scopeConfigMock = $this->getScopeConfigMock();
        $storeManagerMock = $this->getStoreManagerMock();
        $generateAccessTokenMock = $this->getGenerateAccessTokenMock();
        $refIdBuilderMock = $this->getRefIdBuilderMock();
        $checkoutSessionMock = $this->getCheckoutSessionMock();
        $serializerMock = $this->getSerializerMock();
        $loggerMock = $this->getLoggerMock();

        $this->getCheckoutConfigProviderModelObject(
            $scopeConfigMock,
            $storeManagerMock,
            $generateAccessTokenMock,
            $refIdBuilderMock,
            $checkoutSessionMock,
            $serializerMock,
            $loggerMock
        );

        $getConfigExecution = $this->checkoutConfigProvider->getConfig();
        $this->assertIsArray($getConfigExecution);
        $this->assertArrayHasKey('payment', $getConfigExecution);
        $this->assertArrayHasKey('payyourway', $getConfigExecution['payment']);
        $this->assertArrayHasKey('refid', $getConfigExecution['payment']['payyourway']);
        $this->assertArrayHasKey('sdkUrl', $getConfigExecution['payment']['payyourway']);
        $this->assertArrayHasKey('isActive', $getConfigExecution['payment']['payyourway']);
        $this->assertArrayHasKey('clientId', $getConfigExecution['payment']['payyourway']);
        $this->assertArrayHasKey('environment', $getConfigExecution['payment']['payyourway']);
        $this->assertArrayHasKey('currency', $getConfigExecution['payment']['payyourway']);
    }
}
