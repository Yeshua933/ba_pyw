<?php

namespace PayYourWay\Pyw\Controller\Adminhtml\Configuration;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use PayYourWay\pyw\Api\ConfigInterface;
use PayYourWay\pyw\Api\GenerateAccessTokenInterface;
use Psr\Log\LoggerInterface;

class GenerateAccess implements HttpPostActionInterface
{
    private GenerateAccessTokenInterface $generateAccessToken;
    private JsonFactory $jsonFactory;
    private LoggerInterface $logger;


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigInterface $config,
        GenerateAccessTokenInterface $generateAccessToken,
        JsonFactory         $jsonFactory,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            $accessToken = $this->generateAccessToken->execute();
            $this->config->saveAccessToken($accessToken);
        } catch (Exception $exception) {
            $this->logger->error(
                'Unable to save access Token',
                [
                    'exception' => (string)$exception,
                ]);
        }
        return $result->setData(['status' => 200]);
    }
}
