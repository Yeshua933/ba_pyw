<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
namespace PayYourWay\Pyw\Controller\Adminhtml\Configuration;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use PayYourWay\pyw\Api\GenerateAccessTokenInterface;
use Psr\Log\LoggerInterface;

class GenerateAccess implements HttpPostActionInterface
{
    private GenerateAccessTokenInterface $generateAccessToken;
    private JsonFactory $jsonFactory;
    private LoggerInterface $logger;

    public function __construct(
        GenerateAccessTokenInterface $generateAccessToken,
        JsonFactory $jsonFactory,
        LoggerInterface $logger
    ) {
        $this->generateAccessToken = $generateAccessToken;
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            $accessToken = $this->generateAccessToken->execute();
        } catch (Exception $exception) {
            $this->logger->error(
                'Unable to save access Token',
                [
                    'exception' => (string)$exception,
                ]
            );
        }
        return $result->setData(['status' => 200]);
    }
}
