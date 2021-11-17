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
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use PayYourWay\Pyw\Model\PaymentMethod;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use Psr\Log\LoggerInterface;
use PayYourWay\Pyw\Api\RequestInterface as PaymentConfirmationRequestInterface;
use PayYourWay\Pyw\Model\Config;

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
    private PaymentConfirmationRequestInterface $paymentConfirmationRequest;
    private MessageManagerInterface $messageManager;
    private RedirectFactory $redirectFactory;
    private Config $config;
    private GenerateAccessToken $generateAccessToken;
    private RefIdBuilderInterface $refIdBuilder;

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
        PaymentConfirmationRequestInterface $paymentConfirmationRequest,
        MessageManagerInterface $messageManager,
        RedirectFactory $redirectFactory,
        Config $config,
        GenerateAccessToken $generateAccessToken,
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
        $this->paymentConfirmationRequest = $paymentConfirmationRequest;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        $this->refIdBuilder = $refIdBuilder;
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

    private function checkPaymentConfirmation(): bool
    {
        /**
         * Make a request to Payment Confirmation API in order to check the payment details
         */
        $this->paymentConfirmationRequest->setChannel('ONLINE');
        $this->paymentConfirmationRequest->setMerchantId($this->config->getClientId());
        $this->paymentConfirmationRequest->setPywid($this->request->getParam('pywid'));
        $this->paymentConfirmationRequest->setTransactionId($this->quote->getId());
        $this->paymentConfirmationRequest->setActionType('READONLY');
        $this->paymentConfirmationRequest->setTransactionType('1P');
        $this->paymentConfirmationRequest->setRefId($this->refIdBuilder->buildRefId(
            $this->config->getClientId(),
            $this->generateAccessToken->execute(),
            $this->config->getClientId(),
            time(),
            $this->quote->getId(),
            $this->quote->getCustomerEmail()
        ));

        $paymentConfirmationResponse = json_decode($this->paymentConfirmationLookup->lookup(
            $this->paymentConfirmationRequest
        ));

        if (!is_object($paymentConfirmationResponse)) {
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

        if ((float)$paymentConfirmationResponse->paymentTotal !== $this->quote->getGrandTotal()) {
            $this->logger->error(
                'The amount returned from PayYour Way doesn\'t match the amount on the store.',
                [
                    'paymentResponse' => $paymentConfirmationResponse,
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
}
