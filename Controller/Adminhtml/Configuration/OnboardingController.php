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
use Psr\Log\LoggerInterface;

class OnboardingController implements HttpPostActionInterface
{

    protected ConfigInterface $config;
    protected Curl $_curl;
    protected ResultInterface $result;
    private LoggerInterface $logger;

    public function __construct(
        JsonFactory      $jsonFactory,
        LoggerInterface  $logger,
        ConfigInterface  $config,
        Curl $curl,
        RequestInterface $request
    ) {
        $this->config = $config;
        $this->jsonFactory = $jsonFactory;
        $this->_curl = $curl;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        //todo refactor controller: separate business logic.
        $clientName = $this->request->getParam('client_name');
        $clientEmail = $this->request->getParam('email');
        $clientId = $this->request->getParam('client_id');
        $phoneNumber = $this->request->getParam('phone_number');
        $passwordFlag = 'false';
        $secretCodeFlag = 'true';
        $publicKey = $this->request->getParam('public_key');
        $privateKey = $this->request->getParam('private_key');
        $storeId = $this->request->getParam('storeId', 0);
        $environment = $this->request->getParam('environment');

        $publicKey = str_replace(
            ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\r\n", "\n", "\r", " "],
            '',
            $publicKey
        );
        $publicKey = chunk_split($publicKey, 64);
        $publicKey = "-----BEGIN CERTIFICATE-----\n$publicKey-----END CERTIFICATE-----\n";

        $privateKey = str_replace(
            ["-----BEGIN PRIVATE KEY-----", "-----END PRIVATE KEY-----", "\r\n", "\n", "\r", " "],
            '',
            $privateKey
        );
        $privateKey = chunk_split($privateKey, 64);
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n$privateKey-----END RSA PRIVATE KEY-----\n";
        $this->config->savePrivateKey($privateKey);

        $params = ([
            "clientName" => $clientName,
            "clientEmail" => $clientEmail,
            "clientId" => $clientId,
            "phoneNumber" => $phoneNumber,
            "password" => "",
            "passwordFlag" => $passwordFlag,
            "secretCodeFlag" => $secretCodeFlag,
            "publicKey" => $publicKey
        ]);

        $accessToken = $this->config->getAccessToken();
        $url = 'https://oauthweb.uat.telluride.transformco.com/oauthUI/register/registerClient';
        $this->_curl->addHeader("Content-Type", "application/json");
        $this->_curl->addHeader("Authorization", "Bearer " . $accessToken);

        $params = json_encode($params, JSON_THROW_ON_ERROR);

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
}
