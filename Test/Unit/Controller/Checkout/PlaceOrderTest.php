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
use Magento\Sales\Model\Order\Payment;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
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
    private const GRAND_TOTAL = 199.99;
    private const CUSTOMER_EMAIL = 'john@doe.com';
    private const ACCESS_TOKEN = 'WTFvrq5BS5isEuQJqXQuAjVp';
    private const ENVIRONMENT = 'sandbox';
    private const REF_ID = '428ZvUu6gFP76Lhm8t1co2joIsGgurfQY82SjTB1yG0Mi3p7PZQCJwxIgbq';

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
        $this->checkoutSession = $this->createMock(CheckoutSession::class);
        $this->checkoutSession
            ->expects($this->any())
            ->method('getQuote')
            ->willReturn($this->quote);

        $this->checkoutSession
            ->expects($this->any())
            ->method('getQuoteId')
            ->willReturn(self::QUOTE_ID);
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
                'collectTotals'
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
            ->method('getId')
            ->willReturn(
                null,
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

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->quoteManagement = $this->createMock(CartManagementInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->redirect = $this->createMock(RedirectInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->paymentConfirmationLookup = $this->createMock(PaymentConfirmationLookupInterface::class);
        $this->paymentConfirmationRequestFactory = $this->createMock(PaymentConfirmationRequestInterfaceFactory::class);
        $this->messageManager = $this->createMock(MessageManagerInterface::class);
        $this->redirectFactory = $this->createMock(RedirectFactory::class);
        $this->serializer = $this->createMock(SerializerInterface::class);

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
