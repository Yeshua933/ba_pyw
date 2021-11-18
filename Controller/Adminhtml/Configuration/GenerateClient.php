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
use PayYourWay\pyw\Api\ConfigInterface;
use Psr\Log\LoggerInterface;

class GenerateClient implements HttpPostActionInterface
{
    private JsonFactory $jsonFactory;
    private LoggerInterface $logger;
    private ConfigInterface $config;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigInterface $config,
        JsonFactory         $jsonFactory,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            $clientId = $this->config->generateClientId();
            $this->config->saveClientId($clientId);
        } catch (Exception $exception) {
            $this->logger->error(
                'Unable to save to generate Client Id',
                [
                    'exception' => (string)$exception,
                ]
            );
            return $result->setData(['status' => 400]);
        }
        return $result->setData(['status' => 200]);
    }
}
