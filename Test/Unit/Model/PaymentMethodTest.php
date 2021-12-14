<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Model;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Model\Order;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\PaymentReturnLookupInterface;
use PayYourWay\Pyw\Api\PaymentReturnRequestInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Api\RequestInterface as PaymentConfirmationRequestInterface;
use PayYourWay\Pyw\Model\PaymentMethod;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PaymentMethodTest extends TestCase
{
    /** @var ObjectManager|MockObject */
    private $objectManager;

    /** @var PaymentMethod */
    private $paymentMethodModel;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
    }

    private function getConfigMockForRefund(): ConfigInterface
    {
        $clientId = 'PYW';
        $environment = 'sandbox';

        $config = $this->createMock(ConfigInterface::class);
        $config
            ->expects($this->exactly(5))
            ->method('getClientId')
            ->willReturn($clientId);

        $config
            ->expects($this->once())
            ->method('getEnvironment')
            ->willReturn($environment);

        return $config;
    }

    private function getContextMockForRefund(): Context
    {
        return $this->createMock(Context::class);
    }

    private function getCustomAttributeFactoryMockForRefund(): AttributeValueFactory
    {
        return $this->createMock(AttributeValueFactory::class);
    }

    private function getDirectoryMockForRefund(): DirectoryHelper
    {
        return $this->createMock(DirectoryHelper::class);
    }

    private function getExtensionFactoryMockForRefund(): ExtensionAttributesFactory
    {
        return $this->createMock(ExtensionAttributesFactory::class);
    }

    private function getGenerateAccessTokenMockForRefund(): GenerateAccessTokenInterface
    {
        $accessToken = 'ACCESS_TOKEN_RANDOM_STRING';

        $generateAccessToken = $this->createMock(GenerateAccessTokenInterface::class);
        $generateAccessToken
            ->expects($this->exactly(2))
            ->method('execute')
            ->willReturn($accessToken);

        return $generateAccessToken;
    }

    private function getLoggerMockForRefund(): Logger
    {
        return $this->createMock(Logger::class);
    }

    private function getPaymentConfirmationLookupMockForRefund(): PaymentConfirmationLookupInterface
    {
        $paymentConfirmationReturnJson = '{"authCode":"09180-20211117-843154488","orderDate":"11-17-2021 04:28:52","paymentStatus":"PENDING","paymentTotal":"500.00","paymentDetails":[{"id":"12e28334-4dd4-4510-a40e-bf8d2c39ef55","type":"CREDITCARD","amount":"500.00","status":"SUCCESS","currency":"USD","cardLastFour":"9699","cardType":"Sears"},{"id":"16371463","type":"SYWR","amount":"0.0","status":"NOT_PROCESSED","currency":"USD"}]}'; //phpcs:ignore

        $paymentConfirmationLookup = $this->createMock(PaymentConfirmationLookupInterface::class);
        $paymentConfirmationLookup
            ->expects($this->once())
            ->method('lookup')
            ->willReturn($paymentConfirmationReturnJson);

        return $paymentConfirmationLookup;
    }

    private function getPaymentConfirmationRequestMockForRefund(): PaymentConfirmationRequestInterface
    {
        $channel = 'ONLINE';
        $merchantId = 'PYW';
        $pywId = '12e28334-4dd4-4510-a40e-bf8d2c39ef55';
        $orderId = '45';
        $actionType = 'READONLY';
        $transactionType = '1P';
        $refId = 'REF_ID_RANDOM_STRING';

        $paymentConfirmationRequest = $this->createMock(PaymentConfirmationRequestInterface::class);
        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setChannel')
            ->with($channel)
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setMerchantId')
            ->with($merchantId)
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setPywid')
            ->with($pywId)
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setTransactionId')
            ->with($orderId)
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setActionType')
            ->with($actionType)
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setTransactionType')
            ->with($transactionType)
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setRefId')
            ->with($refId)
            ->willReturnSelf();

        return $paymentConfirmationRequest;
    }

    private function getPaymentDataMockForRefund(): Data
    {
        return $this->createMock(Data::class);
    }

    private function getPaymentReturnLookupMockForRefund(): PaymentReturnLookupInterface
    {
        $paymentReturnResponse = "{}";

        $paymentReturnLookup = $this->createMock(PaymentReturnLookupInterface::class);
        $paymentReturnLookup
            ->expects($this->once())
            ->method('lookup')
            ->willReturn($paymentReturnResponse);

        return $paymentReturnLookup;
    }

    private function getPaymentReturnRequestMockForRefund(): PaymentReturnRequestInterface
    {
        $contentType = 'application/json';
        $accept = 'application/json';
        $channel = 'PYW_ONLINE';
        $clientId = 'PYW';
        $orderId = '45';
        $refId = 'REF_ID_RANDOM_STRING';
        $authCode = '09180-20211117-843154488';
        $amount = 199.99;

        $paymentReturnRequest = $this->createMock(PaymentReturnRequestInterface::class);
        $paymentReturnRequest
            ->expects($this->once())
            ->method('setContentType')
            ->with($contentType)
            ->willReturnSelf();

        $paymentReturnRequest
            ->expects($this->once())
            ->method('setAccept')
            ->with($accept)
            ->willReturnSelf();

        $paymentReturnRequest
            ->expects($this->once())
            ->method('setChannel')
            ->with($channel)
            ->willReturnSelf();

        $paymentReturnRequest
            ->expects($this->once())
            ->method('setClientId')
            ->with($clientId)
            ->willReturnSelf();

        $paymentReturnRequest
            ->expects($this->once())
            ->method('setTransactionId')
            ->with($orderId)
            ->willReturnSelf();

        $paymentReturnRequest
            ->expects($this->once())
            ->method('setRefId')
            ->with($refId)
            ->willReturnSelf();

        $paymentReturnRequest
            ->expects($this->once())
            ->method('setAuthCode')
            ->with($authCode)
            ->willReturnSelf();

        $paymentReturnRequest
            ->expects($this->once())
            ->method('setReturnAmount')
            ->with($amount)
            ->willReturnSelf();

        $paymentReturnRequest
            ->expects($this->once())
            ->method('setReturnPayments')
            ->with([])
            ->willReturnSelf();

        return $paymentReturnRequest;
    }

    private function getPywLoggerMockForRefund(): LoggerInterface
    {
        $pywLogger = $this->createMock(LoggerInterface::class);
        $pywLogger
            ->expects($this->exactly(2))
            ->method('debug')
            ->willReturnSelf();

        return $pywLogger;
    }

    private function getRefIdBuilderMockForRefund(): RefIdBuilderInterface
    {
        $refId = 'REF_ID_RANDOM_STRING';
        $clientId = 'PYW';
        $accessToken = 'ACCESS_TOKEN_RANDOM_STRING';
        $orderId = '45';
        $customerEmail = '';

        $refIdBuilder = $this->createMock(RefIdBuilderInterface::class);
        $refIdBuilder
            ->expects($this->exactly(2))
            ->method('buildRefId')
            ->with(
                $clientId,
                $accessToken,
                $clientId,
                time(),
                $orderId,
                $customerEmail
            )
            ->willReturn($refId);

        return $refIdBuilder;
    }

    private function getRegistryMockForRefund(): Registry
    {
        return $this->createMock(Registry::class);
    }

    private function getResourceCollectionMockForRefund(): AbstractDb
    {
        return $this->createMock(AbstractDb::class);
    }

    private function getResourceMockForRefund(): AbstractResource
    {
        return $this->createMock(AbstractResource::class);
    }

    private function getScopeConfigMockForRefund(): ScopeConfigInterface
    {
        return $this->createMock(ScopeConfigInterface::class);
    }

    private function getPaymentMethodModelObject(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        PaymentReturnRequestInterface $paymentReturnRequest,
        RefIdBuilderInterface $refIdBuilder,
        ConfigInterface $config,
        GenerateAccessTokenInterface $generateAccessToken,
        PaymentReturnLookupInterface $paymentReturnLookup,
        PaymentConfirmationRequestInterface $paymentConfirmationRequest,
        PaymentConfirmationLookupInterface $paymentConfirmationLookup,
        LoggerInterface $pywLogger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ): void {
        $this->paymentMethodModel = $this->objectManager->getObject(
            PaymentMethod::class,
            [
                'context' => $context,
                'registry' => $registry,
                'extensionFactory' => $extensionFactory,
                'customAttributeFactory' => $customAttributeFactory,
                'paymentData' => $paymentData,
                'scopeConfig' => $scopeConfig,
                'logger' => $logger,
                'paymentReturnRequest' => $paymentReturnRequest,
                'refIdBuilder' => $refIdBuilder,
                'config' => $config,
                'generateAccessToken' => $generateAccessToken,
                'paymentReturnLookup' => $paymentReturnLookup,
                'paymentConfirmationRequest' => $paymentConfirmationRequest,
                'paymentConfirmationLookup' => $paymentConfirmationLookup,
                'pywLogger' => $pywLogger,
                'resource' => $resource,
                'resourceCollection' => $resourceCollection,
                'data' => $data,
                'directory' => $directory
            ]
        );
    }

    public function testRefund()
    {
        $contextMock = $this->getContextMockForRefund();
        $registryMock = $this->getRegistryMockForRefund();
        $extensionFactoryMock = $this->getExtensionFactoryMockForRefund();
        $customAttributeFactoryMock = $this->getCustomAttributeFactoryMockForRefund();
        $paymentDataMock = $this->getPaymentDataMockForRefund();
        $scopeConfigMock = $this->getScopeConfigMockForRefund();
        $loggerMock = $this->getLoggerMockForRefund();
        $paymentReturnRequestMock = $this->getPaymentReturnRequestMockForRefund();
        $refIdBuilderMock = $this->getRefIdBuilderMockForRefund();
        $configMock = $this->getConfigMockForRefund();
        $generateAccessTokenMock = $this->getGenerateAccessTokenMockForRefund();
        $paymentReturnLookupMock = $this->getPaymentReturnLookupMockForRefund();
        $paymentConfirmationRequestMock = $this->getPaymentConfirmationRequestMockForRefund();
        $paymentConfirmationLookupMock = $this->getPaymentConfirmationLookupMockForRefund();
        $pywLoggerMock = $this->getPywLoggerMockForRefund();
        $resourceMock = $this->getResourceMockForRefund();
        $resourceCollectionMock = $this->getResourceCollectionMockForRefund();
        $dataMock = [];
        $directoryMock = $this->getDirectoryMockForRefund();

        $this->getPaymentMethodModelObject(
            $contextMock,
            $registryMock,
            $extensionFactoryMock,
            $customAttributeFactoryMock,
            $paymentDataMock,
            $scopeConfigMock,
            $loggerMock,
            $paymentReturnRequestMock,
            $refIdBuilderMock,
            $configMock,
            $generateAccessTokenMock,
            $paymentReturnLookupMock,
            $paymentConfirmationRequestMock,
            $paymentConfirmationLookupMock,
            $pywLoggerMock,
            $resourceMock,
            $resourceCollectionMock,
            $dataMock,
            $directoryMock
        );

        $orderId = '45';
        $pywId = '12e28334-4dd4-4510-a40e-bf8d2c39ef55';
        $amount = 199.99;

        $orderMock = $this->createMock(Order::class);
        $orderMock
            ->expects($this->exactly(5))
            ->method('getId')
            ->willReturn($orderId);

        $paymentMock = $this->getMockBuilder(InfoInterface::class)
            ->setMethods([
                'getOrder',
                'getLastTransId'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $paymentMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);

        $paymentMock
            ->expects($this->once())
            ->method('getLastTransId')
            ->willReturn($pywId);

        $refundExecution = $this->paymentMethodModel->refund($paymentMock, $amount);
        $this->assertInstanceOf(PaymentMethod::class, $refundExecution);
    }
}
