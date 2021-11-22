<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Widget\Button;
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

    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        //todo fix title
        $title = __('Register');
        $envId = 'select-groups-payyourway-fields-environment-value';
        $storeId = 0;

        if ($this->getRequest()->getParam('website')) {
            $website = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'));
            if ($website->getId()) {
                /** @var Store $store */
                $store = $website->getDefaultStore();
                $storeId = $store->getStoreId();
            }
        }

        $endpoint = $this->getUrl('payyourway/configuration/onboardingcontroller', ['storeId' => $storeId]);

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

    public function getOnboardingUrl(): string
    {
        return $this->getUrl('payyourway/controller/onboarding');
    }

    public function getButtonHtml(): string
    {
        $button = $this->getLayout()
            ->createBlock(Button::class)
            ->setData(['id' => 'sign_up', 'label' => __('REGISTER FOR PAY YOUR WAY'),]);
        return $button->toHtml();
    }
}
