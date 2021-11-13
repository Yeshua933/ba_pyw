<?php

namespace PayYourWay\Pyw\Controller\Adminhtml\Configuration;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PayYourWay\Pyw\Model\Config;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use Magento\Framework\Controller\Result\JsonFactory;

class GenerateAccess  implements HttpPostActionInterface
{
    private GenerateAccessToken $generateAccessToken;
    private JsonFactory $jsonFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $config,
        GenerateAccessToken $generateAccessToken,
        JsonFactory         $jsonFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @throws \JsonException
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            $accessToken = $this->generateAccessToken->execute();
            $this->config->saveAccessToken($accessToken);
        } catch (Exception $exception) {

        }
        return $result->setData(['status' => 200]);
    }
}
