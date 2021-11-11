<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Framework\DataObject;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class PaymentMethod extends AbstractMethod
{
    public const METHOD_CODE = 'payyourway';

    /** @var string */
    protected $_code = self::METHOD_CODE;
    protected $_canCapture = true;

    public function assignData(DataObject $data): PaymentMethod
    {
        if (is_array($data->getAdditionalData())) {
            foreach ($data->getAdditionalData() as $key => $value) {
                $this->getInfoInstance()->setAdditionalInformation($key, $value);
            }
        }
        return parent::assignData($data);
    }

    public function capture(InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);
        $additionalInformation = $payment->getAdditionalInformation();
        $payment->setTransactionId($additionalInformation['pywid']);
        $payment->setLastTransId($additionalInformation['pywid']);
        return $this;
    }
}
