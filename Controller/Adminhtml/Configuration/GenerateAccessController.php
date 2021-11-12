<?php

namespace PayYourWay\Pyw\Controller\Adminhtml\Configuration;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PayYourWay\Pyw\Model\Config;
use PayYourWay\Pyw\Model\GenerateAccessToken;

class GenerateAccessController extends Action
{
    private GenerateAccessToken $generateAccessToken;

    public function __construct(
        Context     $context,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        GenerateAccessToken $generateAccessToken
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        parent::__construct($context);
    }

    /**
     * @throws \JsonException
     */
    public function execute()
    {
        // TODO: Implement execute() method.
        $this->generateAccessToken->execute();
    }

}
