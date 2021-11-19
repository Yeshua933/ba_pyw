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
use Psr\Log\LoggerInterface;

class ChangeClientId implements HttpPostActionInterface
{
    private JsonFactory $jsonFactory;
    private LoggerInterface $logger;
    private ConfigInterface $config;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigInterface      $config,
        JsonFactory          $jsonFactory,
        LoggerInterface      $logger
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            //todo Get value from frontend
            $this->config->saveClientId('Test');
            return $result->setData(['status' => 200]);
        } catch (Exception $exception) {
            return $result->setHttpResponseCode(404);
        }
    }
}
