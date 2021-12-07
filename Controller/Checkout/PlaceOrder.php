<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Controller\Checkout;

use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Api\RequestInterfaceFactory as PaymentConfirmationRequestInterfaceFactory;
use PayYourWay\Pyw\Model\Adminhtml\Config\Source\Environment;
use PayYourWay\Pyw\Model\Config;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use PayYourWay\Pyw\Model\PaymentMethod;
use PayYourWay\Pyw\Model\PaymentMethod as PayYourWayPaymentMethod;
use Psr\Log\LoggerInterface;

class PlaceOrder implements HttpGetActionInterface
{
    private CartManagementInterface $quoteManagement;
    private CheckoutSession $checkoutSession;
    private CustomerSession $customerSession;
    private CartRepositoryInterface $quoteRepository;
    private ResponseInterface $response;
    private RedirectInterface $redirect;
    private CartInterface $quote;
    private LoggerInterface $logger;
    private RequestInterface $request;
    private PaymentConfirmationLookupInterface $paymentConfirmationLookup;
    private PaymentConfirmationRequestInterfaceFactory $paymentConfirmationRequestFactory;
    private MessageManagerInterface $messageManager;
    private RedirectFactory $redirectFactory;
    private Config $config;
    private GenerateAccessToken $generateAccessToken;
    private RefIdBuilderInterface $refIdBuilder;
    private SerializerInterface $serializer;

    public function __construct(
        CartManagementInterface $quoteManagement,
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        Quote $quote,
        ResponseInterface $response,
        RedirectInterface $redirect,
        LoggerInterface $logger,
        RequestInterface $request,
        PaymentConfirmationLookupInterface $paymentConfirmationLookup,
        PaymentConfirmationRequestInterfaceFactory $paymentConfirmationRequestFactory,
        MessageManagerInterface $messageManager,
        RedirectFactory $redirectFactory,
        Config $config,
        GenerateAccessToken $generateAccessToken,
        SerializerInterface $serializer,
        RefIdBuilderInterface $refIdBuilder
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->quoteRepository = $quoteRepository;
        $this->quote = $quote;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->response = $response;
        $this->redirect = $redirect;
        $this->logger = $logger;
        $this->request = $request;
        $this->paymentConfirmationLookup = $paymentConfirmationLookup;
        $this->paymentConfirmationRequestFactory = $paymentConfirmationRequestFactory;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        $this->refIdBuilder = $refIdBuilder;
        $this->serializer = $serializer;
    }

    /**
     * Return checkout quote object
     *
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    private function getQuote(): CartInterface
    {
        $quote = null;

        if (!$this->quote->getId()) {
            if ($this->checkoutSession->getQuoteId()) {
                $quote = $this->quoteRepository->get($this->checkoutSession->getQuoteId());
            }
        }

        return $quote;
    }

    /**
     * Instantiate quote
     *
     * @return void
     * @throws LocalizedException
     */
    private function initCheckout()
    {
        $quote = $this->getQuote();

        if ($quote->getPayment()->getMethod() !== PayYourWayPaymentMethod::METHOD_CODE) {
            $this->response->setStatusHeader(403, '1.1', 'Forbidden');
            throw new LocalizedException(
                __('We can\'t initialize the Pay Your Way Checkout due to a payment method mismatch.')
            );
        }

        if (!$quote->hasItems()) {
            $this->response->setStatusHeader(403, '1.1', 'Forbidden');
            throw new LocalizedException(__('We can\'t initialize the Pay Your Way Checkout.'));
        }

        if ($quote->getIsVirtual()) {
            $this->response->setStatusHeader(403, '1.1', 'Forbidden');
            throw new LocalizedException(__('Virtual product are not support by the Pay Your Way Checkout.'));
        }

        if (!$quote->getGrandTotal()) {
            throw new LocalizedException(
                __(
                    'Pay Your Way can\'t process orders with a zero balance due. '
                    . 'To finish your purchase, please go through the standard checkout process.'
                )
            );
        }

        $this->quote = $quote;
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return void
     */
    private function prepareGuestQuote()
    {
        $billingAddress = $this->quote->getBillingAddress();

        $email = $billingAddress->getOrigData('email') !== null
            ? $billingAddress->getOrigData('email') : $billingAddress->getEmail();

        $this->quote->setCustomerId(null)
            ->setCustomerEmail($email)
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);
    }

    /**
     * Submit the order
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        try {
            $this->initCheckout();
        } catch (LocalizedException $exception) {
            $this->logger->error(
                'Something went wrong with the Pay Your Way Checkout.',
                [
                    'quote' => $this->quote->getData(),
                    'exception' => (string)$exception,
                ]
            );

            $this->messageManager->addErrorMessage(
                __('Something went wrong with the Pay Your Way Checkout.')
            );

            $redirect = $this->redirectFactory->create();
            $redirect->setPath('checkout/cart');
            return $redirect;
        }

        $quoteId = $this->quote->getId();

        if (!$this->customerSession->isLoggedIn()) {
            $this->prepareGuestQuote();
        }

        $this->quote->getBillingAddress()->setShouldIgnoreValidation(true);
        $this->quote->setShippingAddress($this->quote->getBillingAddress());
        if (!$this->quote->getIsVirtual()) {
            $this->quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }

        $this->quote->collectTotals();

        if (!$this->checkPaymentConfirmation()) {
            $redirect = $this->redirectFactory->create();
            $redirect->setPath('checkout/cart');
            return $redirect;
        }

        try {
            $this->quote->getPayment()->importData([
                'pywid' => $this->request->getParam('pywid'),
                'method' => PaymentMethod::METHOD_CODE
            ]);
        } catch (LocalizedException $exception) {
            $this->logger->error(
                'Something went wrong with the Pay Your Way Checkout.',
                [
                    'quote' => $this->quote->getData(),
                    'pywid' => $this->request->getParam('pywid'),
                    'exception' => (string)$exception,
                ]
            );
        }

        try {
            $order = $this->quoteManagement->submit($this->quote);
        } catch (LocalizedException | Exception $exception) {
            $this->logger->error(
                'Something went wrong with the Pay Your Way Checkout.',
                [
                    'quote' => $this->quote->getData(),
                    'pywid' => $this->request->getParam('pywid'),
                    'exception' => (string)$exception,
                ]
            );
        }

        if (!$order) {
            $redirect = $this->redirectFactory->create();
            $redirect->setPath('checkout/cart');
            return $redirect;
        }

        $this->checkoutSession->clearHelperData();
        $this->checkoutSession->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

        $this->checkoutSession->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId())
            ->setLastOrderStatus($order->getStatus());

        $redirect = $this->redirectFactory->create();
        $redirect->setPath('checkout/onepage/success');
        return $redirect;
    }

    /**
     * @throws \JsonException
     */
    private function checkPaymentConfirmation(): bool
    {
        $merchantId = $this->config->getClientId();

        if ($merchantId === null) {
            $this->logger->error(
                'There is an issue with the merchant account',
                [
                    'quote' => $this->quote->getData(),
                ]
            );
            $this->messageManager->addErrorMessage(
                __('There is an issue with the merchant account')
            );
            return false;
        }

        /**
         * Make a request to Payment Confirmation API in order to check the payment details
         */

        $paymentConfirmationResponse = $this->paymentConfirmationLookup->lookup(
            $this->createPaymentConfirmationRequest()
        );

        if ($this->config->isDebugMode()) {
            $this->logger->info($paymentConfirmationResponse);
        }

        $paymentConfirmationResponseDecode = $this->serializer->unserialize($paymentConfirmationResponse);

        if (!is_array($paymentConfirmationResponseDecode)) {
            $this->logger->error(
                'There is an issue with the payment gateway provider',
                [
                    'quote' => $this->quote->getData(),
                ]
            );
            $this->messageManager->addErrorMessage(
                __('There is an issue with the payment gateway provider')
            );
            return false;
        }

        if (!empty($paymentConfirmationResponseDecode['errors'])) {
            $this->logger->error(
                'There is an issue with the payment gateway provider.',
                [
                    'payment_response' => $paymentConfirmationResponseDecode,
                    'quote' => $this->quote->getData()
                ]
            );
            $this->messageManager->addErrorMessage(
                __('There is an issue with the payment gateway provider.')
            );
            return false;
        }

        if ((float)$paymentConfirmationResponseDecode['paymentTotal'] !== $this->quote->getGrandTotal()) {
            $this->logger->error(
                'The amount returned from PayYour Way doesn\'t match the amount on the store.',
                [
                    'payment_response' => $paymentConfirmationResponseDecode,
                    'quote' => $this->quote->getData(),
                    'pywid' => $this->request->getParam('pywid')
                ]
            );
            $this->messageManager->addErrorMessage(
                __('The amount returned from PayYour Way doesn\'t match the amount on the store.')
            );
            return false;
        }
        return true;
    }

    /**
     * @throws \JsonException
     */
    private function createPaymentConfirmationRequest(): \PayYourWay\Pyw\Api\RequestInterface
    {
        $accessToken =  $this->generateAccessToken->execute();
        if ($accessToken === null) {
            $accessToken = '';
        }
        $sandboxMode = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX;
        $quoteId = (string) $this->quote->getId();
        $customerEmail = $this->quote->getCustomerEmail() ?? '';

        $refId = $this->getRefId($accessToken, $quoteId, $customerEmail, $sandboxMode);

        /** @var \PayYourWay\Pyw\Api\RequestInterface $paymentConfirmationRequest */
        $paymentConfirmationRequest = $this->paymentConfirmationRequestFactory->create();
        $paymentConfirmationRequest->setChannel('ONLINE');
        $paymentConfirmationRequest->setMerchantId($this->config->getClientId());//TODO remove this
        $paymentConfirmationRequest->setPywid($this->request->getParam('pywid'));
        $paymentConfirmationRequest->setTransactionId((string)$this->quote->getId());
        $paymentConfirmationRequest->setActionType('READONLY');
        $paymentConfirmationRequest->setTransactionType('1P');
        $paymentConfirmationRequest->setRefId($refId);

        return $paymentConfirmationRequest;
    }

    /**
     * Generates RefId
     *
     * @param string $accessToken
     * @param string $quoteId
     * @param string $customerEmail
     * @param bool $sandboxMode
     * @return string
     */
    private function getRefId(
        string $accessToken,
        string $quoteId,
        string $customerEmail = '',
        bool $sandboxMode = false
    ): string {
        $refId = $this->refIdBuilder->buildRefId(
            $this->config->getClientId(),
            $accessToken,
            $this->config->getClientId(),
            time(),
            $quoteId,
            $customerEmail,
            $sandboxMode
        );

        if ($this->config->isDebugMode()) {
            $debug = [
                'client_id'=>$this->config->getClientId(),
                'access_token'=>$accessToken,
                'requestor_id'=>$this->config->getClientId(),
                'timestamp'=>time(),
                'transaction_id'=>$quoteId,
                'user_id'=>$customerEmail ?? '',
                'sandbox_mode'=>$sandboxMode,
                'ref_id'=>$refId
            ];
            $this->logger->info($this->serializer->serialize($debug));
        }

        return $refId;
    }
}
