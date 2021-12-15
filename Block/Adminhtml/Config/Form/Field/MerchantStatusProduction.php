<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;

class MerchantStatusProduction extends Field
{
    private GenerateAccessTokenInterface $generateAccessToken;

    public function __construct(
        Context $context,
        GenerateAccessTokenInterface $generateAccessToken,
        ?SecureHtmlRenderer $secureRenderer = null,
        array $data = []
    ) {
        parent::__construct($context, $data, $secureRenderer);
        $this->generateAccessToken = $generateAccessToken;
    }

    /**
     * Get markup showing status of Merchant production account
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $state = 'critical';
        $status = 'Not Registered';

        /**
         * If token is generated successfully then the merchant is registered
         */
        if ($this->generateAccessToken->execute()) {
            $state = 'notice';
            $status = 'Registered';
        }

        return '<span class="grid-severity-' . $state . '"><span>' . $status . '</span></span>';
    }
}
