<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\ViewModel;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use PayYourWay\Pyw\Model\PaymentMethod as PayYourWayPaymentMethod;

class UpdateSectionViewModel implements ArgumentInterface
{
    private CheckoutSession $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function isLastOrderPayYourWay(): bool
    {
        $lastRealOrder = $this->checkoutSession->getLastRealOrder();

        return $lastRealOrder->getPayment()->getMethod() === PayYourWayPaymentMethod::METHOD_CODE;
    }
}
