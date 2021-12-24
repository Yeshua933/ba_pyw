<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */

namespace PayYourWay\Pyw\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Checkbox extends Field
{
    const CONFIG_PATH = 'payment/payyourway/checkbox';

    protected $_template = 'PayYourWay_Pyw::system/config/checkbox.phtml';

    protected $_values = null;

    /**
     * @param Context $context
     * @param Structure $configStructure
     * @param array $data
     */
    public function __construct(
        Context $context,
        array   $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getValues()
    {
        $values = [];
        $objectManager = ObjectManager::getInstance();

        foreach ($objectManager
                     ->create('PayYourWay\Pyw\Model\Adminhtml\Config\Source\Checkbox')
                     ->toOptionArray() as $value) {
            $values[$value['value']] = $value['label'];
        }

        return $values;
    }

    /**
     *
     * @param  $name
     * @return boolean
     */
    public function getIsChecked($name)
    {
        return in_array($name, $this->getCheckedValues());
    }

    /**
     *
     * get the checked value from config
     */
    public function getCheckedValues()
    {
        if (($this->_values) === null) {
            $data = $this->getConfigData();
            if (isset($data[self::CONFIG_PATH])) {
                $data = $data[self::CONFIG_PATH];
            } else {
                $data = '';
            }
            $this->_values = explode(',', $data);
        }

        return $this->_values;
    }

    /**
     * Retrieve element HTML markup.
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());

        return $this->_toHtml();
    }
}
