<?php

/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */

namespace PayYourWay\Pyw\Controller\Adminhtml\Configuration;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use PayYourWay\pyw\Api\ConfigInterface;
use Psr\Log\LoggerInterface;

class GenerateClientIdController implements HttpPostActionInterface
{
    private JsonFactory $jsonFactory;
    private LoggerInterface $logger;
    private ConfigInterface $config;


    public function __construct(
        ConfigInterface $config,
        JsonFactory         $jsonFactory,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {

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
