<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Custom renderer for PYW Onboarding popup
 */
class PywOnboarding extends Field
{
    /** Path to block template */
    protected $_template = 'PayYourWay_Pyw::system/config/Onboarding.phtml';

    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    public function getCustomUrl(): string
    {
        return $this->getUrl('router/controller/action');
    }

    public function getButtonHtml(): string
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(['id' => 'sign_up', 'label' => __('SIGN UP FOR PAY YOUR WAY'),]);
        return $button->toHtml();
    }
}
