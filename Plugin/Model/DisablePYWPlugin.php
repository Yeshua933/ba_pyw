<?php
/**
 * MethodAvailable class
 *
 * @author  Rakesh Jesadiya
 * @package Rbj_DisableFrontPayment
 */

namespace PayYourWay\Pyw\Plugin\Model;

use PayYourWay\Pyw\Model\PaymentMethod;

class DisablePYWPlugin
{
    private PaymentMethod $pywMethod;

    public function __construct(
        PaymentMethod $pywMethod
    ) {
        $this->pywMethod = $pywMethod;
    }

    /**
     * @param Magento\Payment\Model\MethodList $subject
     * @param $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAvailableMethods(\Magento\Payment\Model\MethodList $subject, $result)
    {
        foreach ($result as $key => $_result) {
            if ($_result->getCode() === "payyourway" && !$this->pywMethod->isPYWAvailable()) {
                unset($result[$key]);
            }
        }
        return $result;
    }
}
