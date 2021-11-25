<?php

/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */

namespace PayYourWay\Pyw\Controller\Adminhtml\Configuration;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use PayYourWay\Pyw\Api\ConfigInterface;

class ChangeClientId implements HttpPostActionInterface
{
    private JsonFactory $jsonFactory;
    private ConfigInterface $config;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigInterface      $config,
        JsonFactory          $jsonFactory
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {

            $this->config->saveClientId($this->config->getMerchantName());
            return $result->setData(['status' => 200]);
        } catch (Exception $exception) {
            return $result->setHttpResponseCode(404);
        }
    }
}
