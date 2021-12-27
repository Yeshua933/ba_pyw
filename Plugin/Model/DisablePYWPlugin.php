<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Plugin\Model;

use Magento\Payment\Model\MethodList;
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
     * @param \Magento\Payment\Model\MethodList $subject
     * @param $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAvailableMethods(MethodList $subject, $result)
    {
        foreach ($result as $key => $_result) {
            if ($_result->getCode() === PaymentMethod::METHOD_CODE && !$this->pywMethod->isPYWAvailable()) {
                unset($result[$key]);
            }
        }
        return $result;
    }
}
