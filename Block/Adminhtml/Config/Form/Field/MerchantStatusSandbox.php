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
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;

class MerchantStatusSandbox extends Field
{
    private GenerateAccessTokenInterface $generateAccessToken;
    private CollectionFactory $collectionFactory;

    public function __construct(
        Context $context,
        GenerateAccessTokenInterface $generateAccessToken,
        ?SecureHtmlRenderer $secureRenderer = null,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data, $secureRenderer);
        $this->generateAccessToken = $generateAccessToken;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get markup showing status of Merchant sandbox account
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            "path",
            ['in'=>["payment/payyourway/client_id_sb", "payment/payyourway/secret_key_sb"]]
        );
        $collection->addFieldToFilter("value", ['neq'=>null]);
        $count = $collection->count();
        $state = 'critical';
        $status = 'Not Registered';
        $helpText = __('Please register your store with Shop Your Way.'); //phpcs:ignore

        if ($count === 2) {
            $state = 'warning';
            $status = 'Registered';
            $helpText = __(
                'Your merchant account has been registered.'
            );
        }

        if ($this->generateAccessToken->execute('', '', true)) {
            $state = 'notice';
            $status = 'Active';
            $helpText = __(
                'Your merchant account is active.'
            );
        }
        $output = <<<HTML
        <span class="grid-severity-$state">
            <span>$status</span>
        </span>
        <small>$helpText</small>
        HTML;
        return $output;
    }
}
