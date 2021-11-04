<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Controller\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

class PlaceOrder implements HttpGetActionInterface
{
    private CartManagementInterface $quoteManagement;
    private CheckoutSession $checkoutSession;
    private CustomerSession $customerSession;
    private CartRepositoryInterface $quoteRepository;
    private ResponseInterface $response;
    private RedirectInterface $redirect;
    private CartInterface $quote;

    public function __construct(
        CartManagementInterface $quoteManagement,
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        Quote $quote,
        ResponseInterface $response,
        RedirectInterface $redirect
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->quoteRepository = $quoteRepository;
        $this->quote = $quote;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->response = $response;
        $this->redirect = $redirect;
    }

    /**
     * Submit the order
     *
     * @return void
     * @throws LocalizedException
     */
    public function execute()
    {
        $this->initCheckout();
        $quoteId = $this->quote->getId();

        if (!$this->customerSession->isLoggedIn()) {
            $this->prepareGuestQuote();
        }

        $this->quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->quote->getIsVirtual()) {
            $this->quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }

        $this->quote->collectTotals();
        $order = $this->quoteManagement->submit($this->quote);
        $this->checkoutSession->clearHelperData();
        $this->checkoutSession->setLastQuoteId($quoteId)->setLastSuccessQuoteId($quoteId);

        if (!$order) {
            return;
        }

        $this->checkoutSession->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId())
            ->setLastOrderStatus($order->getStatus());

        $this->redirect->redirect($this->response, 'checkout/onepage/success', []);
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
            ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);
    }
}
