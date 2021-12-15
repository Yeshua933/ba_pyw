<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */

declare(strict_types=1);

namespace PayYourWay\Pyw\Controller\Adminhtml\Configuration;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\OnboardingLookupInterface;
use PayYourWay\Pyw\Api\OnboardingRequestInterface;
use PayYourWay\Pyw\Api\OnboardingRequestInterfaceFactory;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Api\UpdateMerchantLookupInterface;
use PayYourWay\Pyw\Api\UpdateMerchantRequestInterface;
use PayYourWay\Pyw\Api\UpdateMerchantRequestInterfaceFactory;
use PayYourWay\Pyw\Model\Adminhtml\Config\Source\Environment;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use Psr\Log\LoggerInterface;

class OnboardingController implements HttpPostActionInterface
{
    private ConfigInterface $config;
    private LoggerInterface $logger;
    private JsonFactory $jsonFactory;
    private RequestInterface $request;
    private OnboardingLookupInterface $onboardingLookup;
    private OnboardingRequestInterfaceFactory $onboardingRequestFactory;
    private UpdateMerchantLookupInterface $updateMerchantLookup;
    private UpdateMerchantRequestInterfaceFactory $updateMerchantRequestFactory;
    private SerializerInterface $serializer;
    private GenerateAccessToken $generateAccessToken;
    private RefIdBuilderInterface $refIdBuilder;
    private const MESSAGE_CLIENT_ID_ALREADY_TAKEN = 'The Client ID chosen is already taken';
    private const MESSAGE_ERROR = 'ERROR';

    public function __construct(
        JsonFactory $jsonFactory,
        LoggerInterface $logger,
        ConfigInterface $config,
        RequestInterface $request,
        OnboardingLookupInterface $onboardingLookup,
        OnboardingRequestInterfaceFactory $onboardingRequestFactory,
        UpdateMerchantLookupInterface $updateMerchantLookup,
        UpdateMerchantRequestInterfaceFactory $updateMerchantRequestFactory,
        SerializerInterface $serializer,
        GenerateAccessToken $generateAccessToken,
        RefIdBuilderInterface $refIdBuilder
    ) {
        $this->config = $config;
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
        $this->request = $request;
        $this->onboardingLookup = $onboardingLookup;
        $this->onboardingRequestFactory = $onboardingRequestFactory;
        $this->updateMerchantLookup = $updateMerchantLookup;
        $this->updateMerchantRequestFactory = $updateMerchantRequestFactory;
        $this->serializer = $serializer;
        $this->generateAccessToken = $generateAccessToken;
        $this->refIdBuilder = $refIdBuilder;
    }

    public function execute(): ResultInterface
    {
        $response = $this->jsonFactory->create();

        $onboardingRequest = $this->createOnboardingRequest();
        $result = $this->registerClient($onboardingRequest);
        $resultDecode = json_decode($result);

        if ($this->config->isDebugMode()) {
            $debug = [
                'register_client_response' => $resultDecode,
            ];
            $this->logger->info($this->serializer->serialize($debug));
        }

        if ($resultDecode->status === self::MESSAGE_ERROR &&
            $resultDecode->message === self::MESSAGE_CLIENT_ID_ALREADY_TAKEN) {
            $this->logger->info($this->serializer->serialize($resultDecode));
            $response->setData(self::MESSAGE_CLIENT_ID_ALREADY_TAKEN);
            $response->setHttpResponseCode(400);
            return $response;
        }

        if ($resultDecode->status === self::MESSAGE_ERROR) {
            $this->logger->info($this->serializer->serialize($resultDecode));
            $response->setHttpResponseCode(400);
            return $response;
        }

        if ($resultDecode->status === 'SUCCESS') {
            $secretCode = $resultDecode->data->secretCode;
            $this->config->saveSecretKey($secretCode);
            /**
             * TODO: PYW should activate the client id automatically
             */
            $accessToken = $this->getAccessToken();
            if ($accessToken === null) {
                $accessToken = '';
            }
            $refId = $this->getRefId($accessToken, $secretCode);
            $updateMerchantRequest = $this->createUpdateMerchantRequest($refId);
            $resultMerchant = $this->updateMerchant($updateMerchantRequest);
            $resultMerchantDecode = json_decode($resultMerchant);
            if ($this->config->isDebugMode()) {
                $debug = [
                    'update_merchant_response' => $resultMerchantDecode,
                ];
                $this->logger->info($this->serializer->serialize($debug));
            }
            $response = $response->setData($result);
            $response->setHttpResponseCode(200);
            return $response;
        }

        $response->setHttpResponseCode(400);
        return $response;
    }

    private function registerClient(
        OnboardingRequestInterface $onboardingRequest
    ): string {
        return $this->onboardingLookup->execute($onboardingRequest);
    }

    private function updateMerchant(
        UpdateMerchantRequestInterface $updateMerchantRequest
    ): string {
        return $this->updateMerchantLookup->execute($updateMerchantRequest);
    }

    private function getAccessToken(): ?string
    {
        $clientId = $this->request->getParam('client_id');
        $privateKey = $this->request->getParam('private_key');
        $privateKey = $this->cleanPrivateKey($privateKey);
        return $this->generateAccessToken->execute($clientId, $privateKey);
    }

    private function createOnboardingRequest(): OnboardingRequestInterface
    {
        $clientName = $this->request->getParam('client_name');
        $clientEmail = $this->request->getParam('email');
        $clientId = $this->request->getParam('client_id');
        $phoneNumber = $this->request->getParam('phone_number');
        $publicKey = $this->request->getParam('public_key');
        $environment = $this->request->getParam('environment');
        $publicKey = $this->cleanPublicKey($publicKey);

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
        return $onboardingRequest;
    }

    private function cleanPublicKey(string $publicKey): string
    {
        $publicKey = str_replace(
            ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----", "\r\n", "\n", "\r", " "],
            '',
            $publicKey
        );
        $publicKey = chunk_split($publicKey, 64);
        return "-----BEGIN CERTIFICATE-----\n$publicKey-----END CERTIFICATE-----\n";
    }

    private function cleanPrivateKey(string $privateKey): string
    {
        $privateKey = str_replace(
            ["-----BEGIN PRIVATE KEY-----", "-----END PRIVATE KEY-----", "\r\n", "\n", "\r", " "],
            '',
            $privateKey
        );
        $privateKey = chunk_split($privateKey, 64);
        return "-----BEGIN RSA PRIVATE KEY-----\n$privateKey-----END RSA PRIVATE KEY-----\n";
    }

    private function createUpdateMerchantRequest(string $refId): UpdateMerchantRequestInterface
    {
        $domain = ['domain'=>'domain.test']; //TODO
        $clientName = $this->request->getParam('client_name');
        $clientEmail = $this->request->getParam('email');
        $clientId = $this->request->getParam('client_id');
        $phoneNumber = $this->request->getParam('phone_number');
        $address = $this->request->getParam('address');
        $category = $this->request->getParam('category');
        $environment = $this->request->getParam('environment');

        /** @var \PayYourWay\Pyw\Api\UpdateMerchantRequestInterface $updateMerchantRequest */
        $updateMerchantRequest = $this->updateMerchantRequestFactory->create();
        $updateMerchantRequest->setContentType('application/json');
        $updateMerchantRequest->setAccept('application/json');
        $updateMerchantRequest->setClientId($clientId);
        $updateMerchantRequest->setEmail($clientEmail);
        $updateMerchantRequest->setName($clientName);
        $updateMerchantRequest->setPhone($phoneNumber);
        $updateMerchantRequest->setAddress($address);
        $updateMerchantRequest->setCategory($category);
        $updateMerchantRequest->setEnvironment($environment);
        $updateMerchantRequest->setRefId($refId);
        $updateMerchantRequest->setDomains([$domain]);

        return $updateMerchantRequest;
    }

    /**
     * Generates RefId
     *
     * @param string $accessToken
     * @return string
     */
    private function getRefId(string $accessToken, string $secretCode): string
    {
        $environment = $this->request->getParam('environment');
        $sandboxMode = $environment === Environment::ENVIRONMENT_SANDBOX;
        $clientEmail = $this->request->getParam('email');
        $clientId = $this->request->getParam('client_id');

        $refId = $this->refIdBuilder->buildRefId(
            $clientId,
            $accessToken,
            $clientId,
            time(),
            '',
            $clientEmail,
            $sandboxMode,
            $secretCode
        );

        if ($this->config->isDebugMode()) {
            $debug = [
                'client_id'=>$clientId,
                'access_token'=>$accessToken,
                'requestor_id'=>$clientId,
                'timestamp'=>time(),
                'transaction_id'=>'',
                'user_id'=>$clientEmail,
                'sandbox_mode'=>$sandboxMode,
                'ref_id'=>$refId,
            ];
            $this->logger->info($this->serializer->serialize($debug));
        }

        return $refId;
    }
}
