<?php

declare(strict_types=1);

namespace PayYourWay\Pyw\Model\Adminhtml\Config\Backend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PayYourWay\Pyw\Api\ConfigInterface;

class ClientId extends \Magento\Framework\App\Config\Value
{
    private ScopeConfigInterface $scopeConfig;

    public function beforeSave()
    {
//        $label = $this->getData('field_config/label');
//
//        if ($this->getValue() == '') {
//            throw new \Magento\Framework\Exception\ValidatorException(__($label . ' is required.'));
//        } else if (!is_numeric($this->getValue())) {
//            throw new \Magento\Framework\Exception\ValidatorException(__($label . ' is not a number.'));
//        } else if ($this->getValue() < 0) {
//            throw new \Magento\Framework\Exception\ValidatorException(__($label . ' is less than 0.'));
//        }
//
//        $this->setValue($);

        parent::beforeSave();
    }
}
