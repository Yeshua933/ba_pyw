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
    private const CLIENT_ID = 'PYW';
    private const QUOTE_ID = 50;
    private const GRAND_TOTAL = 500.00;
    private const CUSTOMER_EMAIL = 'john@doe.com';
    private const ACCESS_TOKEN = 'WTFvrq5BS5isEuQJqXQuAjVp';
    private const ENVIRONMENT = 'sandbox';
    private const REF_ID = '428ZvUu6gFP76Lhm8t1co2joIsGgurfQY82SjTB1yG0Mi3p7PZQCJwxIgbq';
    private const PYW_ID = '12e28334-4dd4-4510-a40e-bf8d2c39ef55';
    private const PAYMENT_CONFIRMATION_RETURN_JSON = '
{
    "authCode": "09180-20211117-843154488",
    "orderDate": "11-17-2021 04:28:52",
    "paymentStatus": "PENDING",
    "paymentTotal": "500.00",
    "paymentDetails": [
        {
            "id": "12e28334-4dd4-4510-a40e-bf8d2c39ef55",
            "type": "CREDITCARD",
            "amount": "500.00",
            "status": "SUCCESS",
            "currency": "USD",
            "cardLastFour": "9699",
            "cardType": "Sears"
        },
        {
            "id": "16371463",
            "type": "SYWR",
            "amount": "0.0",
            "status": "NOT_PROCESSED",
            "currency": "USD"
        }
    ]
}
    ';
    private const PAYMENT_CONFIRMATION_RETURN_ARRAY = [
        "authCode" => "09180-20211117-843154488",
        "orderDate" => "11-17-2021 04:28:52",
        "paymentStatus" => "PENDING",
        "paymentTotal" => "500.00",
        "paymentDetails" => [
            [
                "id" => "8ce531e3-5240-4ce4-9401-ad966b532b7e",
                "type" => "CREDITCARD",
                "amount" => "500.00",
                "status" => "SUCCESS",
                "currency" => "USD",
                "cardLastFour" => "9699",
                "cardType" => "Sears"
            ],
            [
                "id" => "16371463",
                "type" => "SYWR",
                "amount" => "0.0",
                "status" => "NOT_PROCESSED",
                "currency" => "USD"
            ]
        ]
    ];

    /** @var CartManagementInterface|MockObject */
    private $quoteManagement;

    /** @var CheckoutSession|MockObject */
    private $checkoutSession;

    /** @var CustomerSession|MockObject */
    private $customerSession;

    /** @var Config|MockObject */
    private $config;

    /** @var GenerateAccessToken|MockObject */
    private $generateAccessToken;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var MessageManagerInterface|MockObject */
    private $messageManager;

    /** @var PaymentConfirmationLookupInterface|MockObject */
    private $paymentConfirmationLookup;

    /** @var PaymentConfirmationRequestInterfaceFactory|MockObject */
    private $paymentConfirmationRequestFactory;

    /** @var PlaceOrder */
    private $placeOrderController;

    /** @var RefIdBuilderInterface|MockObject */
    private $refIdBuilder;

    /** @var RedirectInterface|MockObject */
    private $redirect;

    /** @var RedirectFactory|MockObject */
    private $redirectFactory;

    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var RequestInterface|MockObject */
    private $request;

    /** @var Quote|MockObject */
    private $quote;

    /** @var CartRepositoryInterface|MockObject */
    private $quoteRepository;

    /**@var ObjectManager|MockObject */
    private $objectManager;

    /** @var SerializerInterface|MockObject */
    private $serializer;

    private function getCheckoutSessionMock(): void
    {
        $this->checkoutSession = $this->getMockBuilder(CheckoutSession::class)
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

        $this->checkoutSession
            ->expects($this->any())
            ->method('getQuote')
            ->willReturn($this->quote);

        $this->checkoutSession
            ->expects($this->any())
            ->method('getQuoteId')
            ->willReturn(self::QUOTE_ID);

        $this->checkoutSession
            ->expects($this->exactly(1))
            ->method('clearHelperData');

        $this->checkoutSession
            ->expects($this->exactly(1))
            ->method('setLastRealOrderId')
            ->willReturnSelf();

        $this->checkoutSession
            ->expects($this->exactly(1))
            ->method('setLastQuoteId')
            ->with($this->checkoutSession->getQuoteId())
            ->willReturnSelf();

        $this->checkoutSession
            ->expects($this->exactly(1))
            ->method('setLastSuccessQuoteId')
            ->with($this->checkoutSession->getQuoteId())
            ->willReturnSelf();

        $this->checkoutSession
            ->expects($this->exactly(1))
            ->method('setLastOrderId')
            ->willReturnSelf();

        $this->checkoutSession
            ->expects($this->exactly(1))
            ->method('setLastOrderStatus')
            ->willReturnSelf();
    }

    private function getConfigMock(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->config
            ->expects($this->any())
            ->method('getClientId')
            ->willReturn(self::CLIENT_ID);

        $this->config
            ->expects($this->any())
            ->method('getEnvironment')
            ->willReturn(self::ENVIRONMENT);

        $this->config
            ->expects($this->any())
            ->method('isDebugMode')
            ->willReturn(true);
    }

    private function getCustomerSessionMock(): void
    {
        $this->customerSession = $this->createMock(CustomerSession::class);
        $this->customerSession
            ->expects($this->any())
            ->method('isLoggedIn')
            ->willReturn(false);
    }

    private function getGenerateAccessTokenMock(): void
    {
        $this->generateAccessToken = $this->createMock(GenerateAccessToken::class);
        $this->generateAccessToken
            ->expects($this->any())
            ->method('execute')
            ->willReturn(self::ACCESS_TOKEN);
    }

    private function getPaymentConfirmationLookupMock(): void
    {
        $this->paymentConfirmationLookup = $this->createMock(PaymentConfirmationLookupInterface::class);
        $this->paymentConfirmationLookup
            ->expects($this->any())
            ->method('lookup')
            ->willReturn(self::PAYMENT_CONFIRMATION_RETURN_JSON);
    }

    private function getPaymentConfirmationRequestFactoryMock(): void
    {
        $paymentConfirmationRequest = $this->createMock(PaymentConfirmationRequestInterface::class);

        $this->paymentConfirmationRequestFactory = $this->getMockBuilder(
            PaymentConfirmationRequestInterfaceFactory::class
        )
            ->setMethods([
                'create',
                'setChannel',
                'setMerchantId',
                'setPywid',
                'setTransactionId',
                'setActionType',
                'setTransactionType',
                'setRefId'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentConfirmationRequestFactory
            ->method('create')
            ->willReturn($paymentConfirmationRequest);

        $this->paymentConfirmationRequestFactory
            ->expects($this->any())
            ->method('setChannel')
            ->willReturnSelf();

        $this->paymentConfirmationRequestFactory
            ->expects($this->any())
            ->method('setMerchantId')
            ->willReturnSelf();

        $this->paymentConfirmationRequestFactory
            ->expects($this->any())
            ->method('setPywid')
            ->willReturnSelf();

        $this->paymentConfirmationRequestFactory
            ->expects($this->any())
            ->method('setTransactionId')
            ->willReturnSelf();

        $this->paymentConfirmationRequestFactory
            ->expects($this->any())
            ->method('setActionType')
            ->willReturnSelf();

        $this->paymentConfirmationRequestFactory
            ->expects($this->any())
            ->method('setTransactionType')
            ->willReturnSelf();

        $this->paymentConfirmationRequestFactory
            ->expects($this->any())
            ->method('setRefId')
            ->willReturnSelf();
    }

    private function getPlaceOrderControllerObject(): void
    {
        $this->placeOrderController = $this->objectManager->getObject(
            PlaceOrder::class,
            [
                'quoteManagement' => $this->quoteManagement,
                'quoteRepository' => $this->quoteRepository,
                'quote' => $this->quote,
                'checkoutSession' => $this->checkoutSession,
                'customerSession' => $this->customerSession,
                'response' => $this->response,
                'redirect' => $this->redirect,
                'logger' => $this->logger,
                'request' => $this->request,
                'paymentConfirmationLookup' => $this->paymentConfirmationLookup,
                'paymentConfirmationRequestFactory' => $this->paymentConfirmationRequestFactory,
                'messageManager' => $this->messageManager,
                'redirectFactory' => $this->redirectFactory,
                'config' => $this->config,
                'generateAccessToken' => $this->generateAccessToken,
                'refIdBuilder' => $this->refIdBuilder,
                'serializer' => $this->serializer
            ]
        );
    }

    private function getQuoteManagementMock(): void
    {
        $orderMock = $this->createPartialMock(
            Order::class,
            ['getId', 'getIncrementId', 'getStatus']
        );

        $this->quoteManagement = $this->getMockBuilder(CartManagementInterface::class)
            ->setMethods([
               'submit'
            ])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->quoteManagement
            ->expects($this->any())
            ->method('submit')
            ->willReturn($orderMock);
    }

    private function getQuoteMock()
    {
        $this->quote = $this->getMockBuilder(Quote::class)
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
                'importData'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->quote
            ->expects($this->any())
            ->method('hasItems')
            ->willReturn(true);

        $paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paymentMock
            ->expects($this->any())
            ->method('getMethod')
            ->willReturn(PayYourWayPaymentMethod::METHOD_CODE);

        $this->quote
            ->expects($this->any())
            ->method('getPayment')
            ->willReturn($paymentMock);

        $this->quote
            ->expects($this->any())
            ->method('getIsVirtual')
            ->willReturn(false);

        $this->quote
            ->expects($this->any())
            ->method('getGrandTotal')
            ->willReturn(self::GRAND_TOTAL);

        $this->quote
            ->expects($this->any())
            ->method('importData')
            ->willReturnSelf();

        $this->quote
            ->expects($this->any())
            ->method('getId')
            ->willReturn(
                null,
                self::QUOTE_ID,
                self::QUOTE_ID,
                self::QUOTE_ID
            );

        $billingAddressMock = $this->getMockBuilder(Address::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getOrigData',
                'getEmail'
            ])
            ->getMock();

        $billingAddressMock
            ->expects($this->any())
            ->method('getOrigData')
            ->willReturn('email')
            ->willReturn(null);

        $billingAddressMock
            ->expects($this->any())
            ->method('getEmail')
            ->willReturn(self::CUSTOMER_EMAIL);

        $this->quote
            ->expects($this->any())
            ->method('getBillingAddress')
            ->willReturn($billingAddressMock);

        $this->quote
            ->expects($this->any())
            ->method('getShippingAddress')
            ->willReturn($billingAddressMock);

        $this->quote
            ->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();
    }

    private function getQuoteRepositoryMock(): void
    {
        $this->quoteRepository = $this->createMock(CartRepositoryInterface::class);

        $this->quoteRepository
            ->expects($this->any())
            ->method('get')
            ->willReturn($this->quote);
    }

    private function getRefIdBuilderMock(): void
    {
        $this->refIdBuilder = $this->createMock(RefIdBuilderInterface::class);
        $this->refIdBuilder
            ->method('buildRefId')
            ->willReturn(self::REF_ID);
    }

    private function getRequestMock(): void
    {
        $this->request = $this->createMock(RequestInterface::class);
        $this->request
            ->expects($this->any())
            ->method('getParam')
            ->with('pywid')
            ->willReturn(self::PYW_ID);
    }

    private function getSerializerMock(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->serializer
            ->expects($this->any())
            ->method('unserialize')
            ->willReturn(self::PAYMENT_CONFIRMATION_RETURN_ARRAY);
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->response = $this->createMock(ResponseInterface::class);
        $this->redirect = $this->createMock(RedirectInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->messageManager = $this->createMock(MessageManagerInterface::class);
        $this->redirectFactory = $this->createMock(RedirectFactory::class);

        $this->getQuoteManagementMock();
        $this->getSerializerMock();
        $this->getPaymentConfirmationLookupMock();
        $this->getRequestMock();
        $this->getPaymentConfirmationRequestFactoryMock();
        $this->getRefIdBuilderMock();
        $this->getGenerateAccessTokenMock();
        $this->getCustomerSessionMock();
        $this->getQuoteMock();
        $this->getCheckoutSessionMock();
        $this->getQuoteRepositoryMock();
        $this->getConfigMock();
        $this->getPlaceOrderControllerObject();
    }

    public function testGetClientId()
    {
        $this->assertEquals(self::CLIENT_ID, $this->config->getClientId());
    }

    public function testGetQuote()
    {
        $this->assertEquals($this->quote, $this->checkoutSession->getQuote());
    }

    public function testQuoteHasItems()
    {
        $this->assertTrue($this->quote->hasItems());
    }

    public function testExecute()
    {
        $this->placeOrderController->execute();
    }
}
