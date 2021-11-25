<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */

declare(strict_types=1);

namespace PayYourWay\Pyw\Controller\Adminhtml\Configuration;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\HTTP\Client\Curl;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\OnboardingLookupInterface;
use PayYourWay\Pyw\Api\OnboardingRequestInterface;
use PayYourWay\Pyw\Api\OnboardingRequestInterfaceFactory;
use Psr\Log\LoggerInterface;

class OnboardingController implements HttpPostActionInterface
{
    private ConfigInterface $config;
    private Curl $_curl;
    private LoggerInterface $logger;
    private JsonFactory $jsonFactory;
    private RequestInterface $request;
    private OnboardingLookupInterface $onboardingLookup;
    private OnboardingRequestInterfaceFactory $onboardingRequestFactory;

    public function __construct(
        JsonFactory $jsonFactory,
        LoggerInterface $logger,
        ConfigInterface $config,
        Curl $curl,
        RequestInterface $request,
        OnboardingLookupInterface $onboardingLookup,
        OnboardingRequestInterfaceFactory $onboardingRequestFactory
    ) {
        $this->config = $config;
        $this->jsonFactory = $jsonFactory;
        $this->_curl = $curl;
        $this->logger = $logger;
        $this->request = $request;
        $this->onboardingLookup = $onboardingLookup;
        $this->onboardingRequestFactory = $onboardingRequestFactory;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $clientName = $this->request->getParam('client_name');
        $clientEmail = $this->request->getParam('email');
        $clientId = $this->request->getParam('client_id');
        $phoneNumber = $this->request->getParam('phone_number');
        $publicKey = $this->request->getParam('public_key');
        $environment = $this->request->getParam('environment');

        $publicKey = str_replace(
            ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\r\n", "\n", "\r", " "],
            '',
            $publicKey
        );
        $publicKey = chunk_split($publicKey, 64);
        $publicKey = "-----BEGIN CERTIFICATE-----\n$publicKey-----END CERTIFICATE-----\n";

        /** @var \PayYourWay\Pyw\Api\OnboardingRequestInterface $onboardingRequest */
        $onboardingRequest = $this->onboardingRequestFactory->create();
        $onboardingRequest->setEnvironment($environment);
        $onboardingRequest->setClientName($clientName);
        $onboardingRequest->setClientEmail($clientEmail);
        $onboardingRequest->setClientId($clientId);
        $onboardingRequest->setPhoneNumber($phoneNumber);
        $onboardingRequest->setPublicKey($publicKey);
        $onboardingRequest->setEnvironment($environment);
        $onboardingRequest->setContentType('application/json');

        $result = $this->registerClient($onboardingRequest);

        $response = $this->jsonFactory->create();

        //todo encrypt keys when saving and retrieving

        try {
            $this->_curl->post($url, $params);
            $resultBody = $this->_curl->getBody();
            if (str_contains($resultBody, 'status":"ERROR')) {
                throw new Exception("Something went wrong.");
            }

            if (str_contains($resultBody, 'status":"SUCCESS')) {
                $jsonDecoded = json_decode($resultBody, true);
                $secretCode = $jsonDecoded['data']['secretCode'];
                $this->config->saveSecretKey($secretCode);
            }

            $response = $response->setData($this->_curl->getBody());

            $response->setHttpResponseCode(200);

        } catch (Exception $exception) {
            $response->setHttpResponseCode(400);
            $this->logger->error(
                'Something went wrong',
                [
                    'exception' => (string)$exception,
                ]
            );
        }

        return $response;
    }

    private function registerClient(
        OnboardingRequestInterface $onboardingRequest
    ): string {
        return $this->onboardingLookup->execute($onboardingRequest);
    }
}
