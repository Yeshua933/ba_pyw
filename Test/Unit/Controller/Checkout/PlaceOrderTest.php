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
        $quoteId = 50;

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
            ->expects($this->exactly(4))
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->checkoutSession
            ->expects($this->once())
            ->method('clearHelperData');

        $this->checkoutSession
            ->expects($this->once())
            ->method('setLastRealOrderId')
            ->willReturnSelf();

        $this->checkoutSession
            ->expects($this->once())
            ->method('setLastQuoteId')
            ->with($this->checkoutSession->getQuoteId())
            ->willReturnSelf();

        $this->checkoutSession
            ->expects($this->once())
            ->method('setLastSuccessQuoteId')
            ->with($this->checkoutSession->getQuoteId())
            ->willReturnSelf();

        $this->checkoutSession
            ->expects($this->once())
            ->method('setLastOrderId')
            ->willReturnSelf();

        $this->checkoutSession
            ->expects($this->once())
            ->method('setLastOrderStatus')
            ->willReturnSelf();
    }

    private function getConfigMock(): void
    {
        $clientId = 'PYW';
        $environment = 'sandbox';

        $this->config = $this->createMock(Config::class);
        $this->config
            ->expects($this->exactly(6))
            ->method('getClientId')
            ->willReturn($clientId);

        $this->config
            ->expects($this->once())
            ->method('getEnvironment')
            ->willReturn($environment);

        $this->config
            ->expects($this->exactly(2))
            ->method('isDebugMode')
            ->willReturn(true);
    }

    private function getCustomerSessionMock(): void
    {
        $this->customerSession = $this->createMock(CustomerSession::class);
        $this->customerSession
            ->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(false);
    }

    private function getGenerateAccessTokenMock(): void
    {
        $accessToken = 'WTFvrq5BS5isEuQJqXQuAjVp';

        $this->generateAccessToken = $this->createMock(GenerateAccessToken::class);
        $this->generateAccessToken
            ->expects($this->once())
            ->method('execute')
            ->willReturn($accessToken);
    }

    private function getLoggerMock(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    private function getMessageManagerMock(): void
    {
        $this->messageManager = $this->createMock(MessageManagerInterface::class);
    }

    private function getPaymentConfirmationLookupMock(): void
    {
        $paymentConfirmationReturnJson = '{"authCode":"09180-20211117-843154488","orderDate":"11-17-2021 04:28:52","paymentStatus":"PENDING","paymentTotal":"500.00","paymentDetails":[{"id":"12e28334-4dd4-4510-a40e-bf8d2c39ef55","type":"CREDITCARD","amount":"500.00","status":"SUCCESS","currency":"USD","cardLastFour":"9699","cardType":"Sears"},{"id":"16371463","type":"SYWR","amount":"0.0","status":"NOT_PROCESSED","currency":"USD"}]}'; //phpcs:ignore

        $this->paymentConfirmationLookup = $this->createMock(PaymentConfirmationLookupInterface::class);
        $this->paymentConfirmationLookup
            ->expects($this->once())
            ->method('lookup')
            ->willReturn($paymentConfirmationReturnJson);
    }

    private function getPaymentConfirmationRequestFactoryMock(): void
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

        $this->paymentConfirmationRequestFactory = $this->getMockBuilder(
            PaymentConfirmationRequestInterfaceFactory::class
        )
            ->setMethods([
                'create',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentConfirmationRequestFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($paymentConfirmationRequest);
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
            ->expects($this->once())
            ->method('submit')
            ->willReturn($orderMock);
    }

    private function getQuoteMock()
    {
        $quoteId = 50;
        $grandTotal = 500.00;
        $customerEmail = 'john@doe.com';

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

        $this->quote
            ->expects($this->exactly(2))
            ->method('getPayment')
            ->willReturn($paymentMock);

        $this->quote
            ->expects($this->exactly(2))
            ->method('getIsVirtual')
            ->willReturn(false);

        $this->quote
            ->expects($this->exactly(2))
            ->method('getGrandTotal')
            ->willReturn($grandTotal);

        $this->quote
            ->expects($this->exactly(4))
            ->method('getId')
            ->willReturn(
                null,
                $quoteId,
                $quoteId,
                $quoteId
            );

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

        $this->quote
            ->expects($this->exactly(3))
            ->method('getBillingAddress')
            ->willReturn($billingAddressMock);

        $this->quote
            ->expects($this->once())
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
            ->expects($this->once())
            ->method('get')
            ->willReturn($this->quote);
    }

    private function getRedirectFactoryMock(): void
    {
        $this->redirectFactory = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods([
                'create',
                'setPath'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $redirect = $this->createMock(Redirect::class);

        $this->redirectFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($redirect);
    }

    private function getRedirectMock(): void
    {
        $this->redirect = $this->createMock(RedirectInterface::class);
    }

    private function getRefIdBuilderMock(): void
    {
        $refId = '428ZvUu6gFP76Lhm8t1co2joIsGgurfQY82SjTB1yG0Mi3p7PZQCJwxIgbq';

        $this->refIdBuilder = $this->createMock(RefIdBuilderInterface::class);
        $this->refIdBuilder
            ->expects($this->once())
            ->method('buildRefId')
            ->willReturn($refId);
    }

    private function getRequestMock(): void
    {
        $pywId = '12e28334-4dd4-4510-a40e-bf8d2c39ef55';

        $this->request = $this->createMock(RequestInterface::class);
        $this->request
            ->expects($this->exactly(2))
            ->method('getParam')
            ->with('pywid')
            ->willReturn($pywId);
    }

    private function getResponseMock(): void
    {
        $this->response = $this->createMock(ResponseInterface::class);
    }

    private function getSerializerMock(): void
    {
        $paymentConfirmationReturnArray = ["authCode"=>"09180-20211117-843154488","orderDate"=>"11-17-2021 04:28:52","paymentStatus"=>"PENDING","paymentTotal"=>"500.00","paymentDetails"=>[["id"=>"8ce531e3-5240-4ce4-9401-ad966b532b7e","type"=>"CREDITCARD","amount"=>"500.00","status"=>"SUCCESS","currency"=>"USD","cardLastFour"=>"9699","cardType"=>"Sears"],["id"=>"16371463","type"=>"SYWR","amount"=>"0.0","status"=>"NOT_PROCESSED","currency"=>"USD"]]]; //phpcs:ignore

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->serializer
            ->expects($this->once())
            ->method('unserialize')
            ->willReturn($paymentConfirmationReturnArray);
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->getResponseMock();
        $this->getRedirectMock();
        $this->getLoggerMock();
        $this->getMessageManagerMock();
        $this->getRedirectFactoryMock();
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

    public function testPlaceOrderControllerRedirect()
    {
        $redirect = $this->createMock(Redirect::class);

        $placeOrderExecution = $this->placeOrderController->execute();

        $this->assertIsObject($placeOrderExecution);
        $this->assertEquals($redirect, $placeOrderExecution);
    }
}
