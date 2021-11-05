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
use Magento\Store\Model\Store;

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
        $title = __('Validate Credentials');
        $envId = 'select-groups-payyourway-fields-environments-value';
        $storeId = 0;

        if ($this->getRequest()->getParam('website')) {
            $website = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'));
            if ($website->getId()) {
                /** @var Store $store */
                $store = $website->getDefaultStore();
                $storeId = $store->getStoreId();
            }
        }

        $endpoint = $this->getUrl('payyourway/configuration/validate', ['storeId' => $storeId]);

        $html = <<<TEXT
            <button
                type="button"
                title="{$title}"
                class="button"
                onclick="pywValidator.call(this, '{$endpoint}', '{$envId}')">
                <span>{$title}</span>
            </button>
TEXT;
        // @codingStandardsIgnoreEnd

        return $html;    }

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
