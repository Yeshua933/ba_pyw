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

/**
 * Custom renderer for ClientId Button
 */
class ChangeClientId extends Field
{
    /** Path to block template */
    protected $_template = 'PayYourWay_Pyw::system/config/GenerateClientId.phtml';

    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    public function getGenerateClientIdUrl(): string
    {
        return $this->getUrl('payyourway/configuration/generateclient');
    }

    public function getButtonHtml(): string
    {
        $button = $this->getLayout()
            ->createBlock(Button::class)
            ->setData(['id' => 'change_client_id', 'label' => __('Change Client Id'),]);
        return $button->toHtml();
    }
}
