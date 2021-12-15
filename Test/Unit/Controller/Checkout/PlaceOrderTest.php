<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Controller\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Api\RequestInterface as PaymentConfirmationRequestInterface;
use PayYourWay\Pyw\Api\RequestInterfaceFactory as PaymentConfirmationRequestInterfaceFactory;
use PayYourWay\Pyw\Controller\Checkout\PlaceOrder;
use PayYourWay\Pyw\Model\Config;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use PayYourWay\Pyw\Model\PaymentMethod as PayYourWayPaymentMethod;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PlaceOrderTest extends TestCase
{
    /** @var PlaceOrder */
    private $placeOrderController;

    /**@var ObjectManager|MockObject */
    private $objectManager;

    private function getCheckoutSessionMock(): CheckoutSession
    {
        $quoteId = 50;

        $checkoutSession = $this->getMockBuilder(CheckoutSession::class)
            ->setMethods([
                'getQuote',
                'getQuoteId',
                'clearHelperData',
                'setLastRealOrderId',
                'setLastQuoteId',
                'setLastSuccessQuoteId',
                'setLastOrderId',
                'setLastOrderStatus'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $checkoutSession
            ->expects($this->exactly(2))
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $checkoutSession
            ->expects($this->once())
            ->method('clearHelperData');

        $checkoutSession
            ->expects($this->once())
            ->method('setLastRealOrderId')
            ->willReturnSelf();

        $checkoutSession
            ->expects($this->once())
            ->method('setLastQuoteId')
            ->with($quoteId)
            ->willReturnSelf();

        $checkoutSession
            ->expects($this->once())
            ->method('setLastSuccessQuoteId')
            ->with($quoteId)
            ->willReturnSelf();

        $checkoutSession
            ->expects($this->once())
            ->method('setLastOrderId')
            ->willReturnSelf();

        $checkoutSession
            ->expects($this->once())
            ->method('setLastOrderStatus')
            ->willReturnSelf();

        return $checkoutSession;
    }

    private function getConfigMock(): Config
    {
        $clientId = 'PYW';
        $environment = 'sandbox';

        $config = $this->createMock(Config::class);
        $config
            ->expects($this->exactly(6))
            ->method('getClientId')
            ->willReturn($clientId);

        $config
            ->expects($this->once())
            ->method('getEnvironment')
            ->willReturn($environment);

        $config
            ->expects($this->exactly(2))
            ->method('isDebugMode')
            ->willReturn(true);

        return $config;
    }

    private function getCustomerSessionMock(): CustomerSession
    {
        $customerSession = $this->createMock(CustomerSession::class);
        $customerSession
            ->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(false);

        return $customerSession;
    }

    private function getGenerateAccessTokenMock(): GenerateAccessToken
    {
        $accessToken = 'WTFvrq5BS5isEuQJqXQuAjVp';

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

    private function getMessageManagerMock(): MessageManagerInterface
    {
        return $this->createMock(MessageManagerInterface::class);
    }

    private function getPaymentConfirmationLookupMock(): PaymentConfirmationLookupInterface
    {
        $paymentConfirmationReturnJson = '{"authCode":"09180-20211117-843154488","orderDate":"11-17-2021 04:28:52","paymentStatus":"PENDING","paymentTotal":"500.00","paymentDetails":[{"id":"12e28334-4dd4-4510-a40e-bf8d2c39ef55","type":"CREDITCARD","amount":"500.00","status":"SUCCESS","currency":"USD","cardLastFour":"9699","cardType":"Sears"},{"id":"16371463","type":"SYWR","amount":"0.0","status":"NOT_PROCESSED","currency":"USD"}]}'; //phpcs:ignore

        $paymentConfirmationLookup = $this->createMock(PaymentConfirmationLookupInterface::class);
        $paymentConfirmationLookup
            ->expects($this->once())
            ->method('lookup')
            ->willReturn($paymentConfirmationReturnJson);

        return $paymentConfirmationLookup;
    }

    private function getPaymentConfirmationRequestFactoryMock(): PaymentConfirmationRequestInterfaceFactory
    {
        $paymentConfirmationRequest = $this->createMock(PaymentConfirmationRequestInterface::class);
        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setChannel')
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setMerchantId')
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setPywid')
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setTransactionId')
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setActionType')
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setTransactionType')
            ->willReturnSelf();

        $paymentConfirmationRequest
            ->expects($this->once())
            ->method('setRefId')
            ->willReturnSelf();

        $paymentConfirmationRequestFactory = $this->getMockBuilder(
            PaymentConfirmationRequestInterfaceFactory::class
        )
            ->setMethods([
                'create',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentConfirmationRequestFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($paymentConfirmationRequest);

        return $paymentConfirmationRequestFactory;
    }

    private function getPlaceOrderControllerObject(
        ResponseInterface $response,
        RedirectInterface $redirect,
        LoggerInterface $logger,
        MessageManagerInterface $messageManager,
        RedirectFactory $redirectFactory,
        CartManagementInterface $quoteManagement,
        SerializerInterface $serializer,
        PaymentConfirmationLookupInterface $paymentConfirmationLookup,
        RequestInterface $request,
        PaymentConfirmationRequestInterfaceFactory $paymentConfirmationRequestFactory,
        RefIdBuilderInterface $refIdBuilder,
        GenerateAccessToken $generateAccessToken,
        CustomerSession $customerSession,
        Config $config,
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        Quote $quote
    ): void {
        $this->placeOrderController = $this->objectManager->getObject(
            PlaceOrder::class,
            [
                'quoteManagement' => $quoteManagement,
                'quoteRepository' => $quoteRepository,
                'quote' => $quote,
                'checkoutSession' => $checkoutSession,
                'customerSession' => $customerSession,
                'response' => $response,
                'redirect' => $redirect,
                'logger' => $logger,
                'request' => $request,
                'paymentConfirmationLookup' => $paymentConfirmationLookup,
                'paymentConfirmationRequestFactory' => $paymentConfirmationRequestFactory,
                'messageManager' => $messageManager,
                'redirectFactory' => $redirectFactory,
                'config' => $config,
                'generateAccessToken' => $generateAccessToken,
                'refIdBuilder' => $refIdBuilder,
                'serializer' => $serializer
            ]
        );
    }

    private function getQuoteManagementMock(): CartManagementInterface
    {
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getId', 'getIncrementId', 'getStatus']
        );

        $quoteManagement = $this->getMockBuilder(CartManagementInterface::class)
            ->setMethods([
               'submit'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $quoteManagement
            ->expects($this->once())
            ->method('submit')
            ->willReturn($orderMock);

        return $quoteManagement;
    }

    private function getQuoteMock(): Quote
    {
        $quote = $this->getMockBuilder(Quote::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $quote
            ->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        return $quote;
    }

    private function getQuoteRepositoryMock(): CartRepositoryInterface
    {
        $quoteId = 50;
        $grandTotal = 500.00;
        $customerEmail = 'john@doe.com';

        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods([
                'getGrandTotal',
                'hasItems',
                'getMethod',
                'getPayment',
                'getIsVirtual',
                'getId',
                'getBillingAddress',
                'setShippingAddress',
                'getShippingAddress',
                'setShouldIgnoreValidation',
                'collectTotals',
                'importData',
                'setLastQuoteId'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock
            ->expects($this->once())
            ->method('hasItems')
            ->willReturn(true);

        $paymentMock = $this->getMockBuilder(Payment::class)
            ->setMethods([
                'getMethod',
                'importData'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $paymentMock
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn(PayYourWayPaymentMethod::METHOD_CODE);

        $paymentMock
            ->expects($this->once())
            ->method('importData')
            ->willReturnSelf();

        $quoteMock
            ->expects($this->exactly(2))
            ->method('getIsVirtual')
            ->willReturn(false);

        $quoteMock
            ->expects($this->exactly(2))
            ->method('getGrandTotal')
            ->willReturn($grandTotal);

        $quoteMock
            ->expects($this->exactly(3))
            ->method('getId')
            ->willReturn(
                $quoteId,
                $quoteId,
                $quoteId
            );

        $quoteMock
            ->expects($this->exactly(2))
            ->method('getPayment')
            ->willReturn($paymentMock);

        $billingAddressMock = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getOrigData',
                'getEmail'
            ])
            ->getMock();

        $billingAddressMock
            ->expects($this->once())
            ->method('getOrigData')
            ->willReturn('email')
            ->willReturn(null);

        $billingAddressMock
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn($customerEmail);

        $quoteMock
            ->expects($this->exactly(3))
            ->method('getBillingAddress')
            ->willReturn($billingAddressMock);

        $quoteMock
            ->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($billingAddressMock);

        $quoteMock
            ->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $quoteRepository = $this->createMock(CartRepositoryInterface::class);

        $quoteRepository
            ->expects($this->once())
            ->method('get')
            ->willReturn($quoteMock);

        return $quoteRepository;
    }

    private function getRedirectFactoryMock(): RedirectFactory
    {
        $redirectFactory = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods([
                'create',
                'setPath'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $redirect = $this->createMock(Redirect::class);

        $redirectFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($redirect);

        return $redirectFactory;
    }

    private function getRedirectMock(): RedirectInterface
    {
        return $this->createMock(RedirectInterface::class);
    }

    private function getRefIdBuilderMock(): RefIdBuilderInterface
    {
        $refId = '428ZvUu6gFP76Lhm8t1co2joIsGgurfQY82SjTB1yG0Mi3p7PZQCJwxIgbq';

        $refIdBuilder = $this->createMock(RefIdBuilderInterface::class);
        $refIdBuilder
            ->expects($this->once())
            ->method('buildRefId')
            ->willReturn($refId);

        return $refIdBuilder;
    }

    private function getRequestMock(): RequestInterface
    {
        $pywId = '12e28334-4dd4-4510-a40e-bf8d2c39ef55';

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects($this->exactly(2))
            ->method('getParam')
            ->with('pywid')
            ->willReturn($pywId);

        return $request;
    }

    private function getResponseMock(): ResponseInterface
    {
        return $this->createMock(ResponseInterface::class);
    }

    private function getSerializerMock(): SerializerInterface
    {
        $paymentConfirmationReturnArray = ["authCode"=>"09180-20211117-843154488","orderDate"=>"11-17-2021 04:28:52","paymentStatus"=>"PENDING","paymentTotal"=>"500.00","paymentDetails"=>[["id"=>"8ce531e3-5240-4ce4-9401-ad966b532b7e","type"=>"CREDITCARD","amount"=>"500.00","status"=>"SUCCESS","currency"=>"USD","cardLastFour"=>"9699","cardType"=>"Sears"],["id"=>"16371463","type"=>"SYWR","amount"=>"0.0","status"=>"NOT_PROCESSED","currency"=>"USD"]]]; //phpcs:ignore

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('unserialize')
            ->willReturn($paymentConfirmationReturnArray);

        return $serializer;
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $responseMock = $this->getResponseMock();
        $redirectMock = $this->getRedirectMock();
        $loggerMock = $this->getLoggerMock();
        $messageManagerMock = $this->getMessageManagerMock();
        $redirectFactoryMock = $this->getRedirectFactoryMock();
        $quoteManagementMock = $this->getQuoteManagementMock();
        $serializerMock = $this->getSerializerMock();
        $paymentConfirmationLookupMock = $this->getPaymentConfirmationLookupMock();
        $requestMock = $this->getRequestMock();
        $paymentConfirmationRequestFactoryMock = $this->getPaymentConfirmationRequestFactoryMock();
        $refIdBuilderMock = $this->getRefIdBuilderMock();
        $generateAccessTokenMock = $this->getGenerateAccessTokenMock();
        $customerSessionMock = $this->getCustomerSessionMock();
        $quoteMock = $this->getQuoteMock();
        $checkoutSessionMock = $this->getCheckoutSessionMock();
        $quoteRepositoryMock = $this->getQuoteRepositoryMock();
        $configMock = $this->getConfigMock();

        $this->getPlaceOrderControllerObject(
            $responseMock,
            $redirectMock,
            $loggerMock,
            $messageManagerMock,
            $redirectFactoryMock,
            $quoteManagementMock,
            $serializerMock,
            $paymentConfirmationLookupMock,
            $requestMock,
            $paymentConfirmationRequestFactoryMock,
            $refIdBuilderMock,
            $generateAccessTokenMock,
            $customerSessionMock,
            $configMock,
            $quoteRepositoryMock,
            $checkoutSessionMock,
            $quoteMock
        );
    }

    public function testPlaceOrderControllerRedirect()
    {
        $redirect = $this->createMock(Redirect::class);

        $placeOrderExecution = $this->placeOrderController->execute();

        $this->assertIsObject($placeOrderExecution);
        $this->assertEquals($redirect, $placeOrderExecution);
    }
}
