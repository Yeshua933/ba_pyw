<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Serialize\SerializerInterface;
use PayYourWay\Pyw\Api\AccessTokenLookupInterface;
use PayYourWay\Pyw\Api\AccessTokenRequestInterface;
use PayYourWay\Pyw\Api\AccessTokenRequestInterfaceFactory;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Model\Adminhtml\Config\Source\Environment;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken as ResourceModel;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken\Collection;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Used for creating/renewing the Pay Your Way access
 *
 * @api
 */
class GenerateAccessToken implements GenerateAccessTokenInterface
{
    private const OAUTH_UAT = 'https://oauth.uat.telluride.transformco.com/oauthAS/service/oAuth/token.json';
    private const OAUTH_PRD = 'https://oauth.telluride.transformco.com/oauthAS/service/oAuth/token.json';
    private const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';
    private const THRESHOLD = 900000;

    private Config $config;
    private AccessTokenRequestInterfaceFactory $accessTokenRequestFactory;
    private AccessTokenLookupInterface $accessTokenLookup;
    private AccessTokenFactory $accessTokenFactory;
    private ResourceModel $resourceModel;
    private SerializerInterface $serializer;
    private Collection $collection;
    private LoggerInterface $logger;
    private CollectionFactory $collectionFactory;

    public function __construct(
        Config                             $config,
        AccessTokenRequestInterfaceFactory $accessTokenRequestFactory,
        AccessTokenLookupInterface         $accessTokenLookup,
        AccessTokenFactory                 $accessTokenFactory,
        ResourceModel                      $resourceModel,
        SerializerInterface                $serializer,
        Collection                         $collection,
        LoggerInterface                    $logger,
        CollectionFactory                  $collectionFactory
    ) {
        $this->config = $config;
        $this->accessTokenRequestFactory = $accessTokenRequestFactory;
        $this->accessTokenLookup = $accessTokenLookup;
        $this->resourceModel = $resourceModel;
        $this->accessTokenFactory = $accessTokenFactory;
        $this->serializer = $serializer;
        $this->collection = $collection;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $clientId = '', string $privateKey = '', ?bool $isSandbox = null): ?string
    {
        $header = $this->getEncoded(json_encode(['typ' => 'JWT', 'alg' => 'RSA'], JSON_THROW_ON_ERROR));
        $clientId = (!empty($clientId)) ? $clientId : $this->config->getClientId();
        if ($isSandbox === null) {
            $isSandbox = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX;
        }

        $privateKey = (!empty($privateKey)) ? $privateKey : $this->config->getPrivateKey();
        $collection = $this->collectionFactory->create();
        $storedAccessToken = $collection
            ->addFieldToFilter('sandbox', (int)$isSandbox)
            ->addFieldToFilter('merchant_id', $clientId)
            ->getFirstItem();
        $aud = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX ?
            self::OAUTH_UAT :
            self::OAUTH_PRD;
        $isDebugMode = $this->config->isDebugMode();

        $claim = $this->getEncoded(
            json_encode([
                "iss" => $clientId,
                "scope" => "",
                "aud" => $aud,
                "exp" => round(microtime(true) * 1000) + 7200000,
                "iat" => round(microtime(true) * 1000)
            ], JSON_THROW_ON_ERROR)
        );

        $storedAccessToken = $this->validateToken($storedAccessToken);

        if ($this->validateParameters($clientId, $privateKey)) {
            return null;
        }

        $this->debugCheckpoint($isDebugMode, $clientId, $privateKey);

        if ($storedAccessToken === null) {
            $jwtSig = $this->generateJWTSignature($header, $claim, $privateKey);

            $accessTokenDecoded = $this->getAccessTokenRequest($header, $claim, $jwtSig);

            $this->debugCheckpoint($isDebugMode, $clientId, $privateKey, $accessTokenDecoded);

            if (array_key_exists('error', $accessTokenDecoded) && !empty($accessTokenDecoded['error'])) {
                $this->debugCheckpoint($isDebugMode, $clientId, $privateKey, $accessTokenDecoded);
                return null;
            }
            $newAccessToken = $this->saveAccessToken($accessTokenDecoded, $clientId, $isSandbox);

            return $newAccessToken->getAccessToken();
        }

        return $storedAccessToken->getAccessToken();
    }

    private function getEncoded($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * If token is 15 minutes or less from expiring, token is deleted and Null is returned
     * If token is not expired existing string token entity is returned.
     */
    private function validateToken($storedAccessToken): ?object
    {
        if ($storedAccessToken->getId() !== null) {

            $timeNow = (int)microtime(true) * 1000;
            $expirationTime = (int)$storedAccessToken->getExp();
            $difference = $expirationTime - $timeNow;
            if ($difference < 0) {
                $difference = 0;
            }

            /**
             * If the difference is greater or equal than 15 minutes
             */
            if ($difference >= self::THRESHOLD) {
                return $storedAccessToken;
            }

            try {
                $this->resourceModel->delete($storedAccessToken);
                return null;
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }

    private function validateParameters(
        ?string $clientId,
        ?string $privateKey
    ): bool {
        return ($clientId === null || $privateKey === null);
    }

    private function debugCheckpoint($isDebugMode, $clientId = '', $privateKey = '', $response = ''): void
    {
        if ($isDebugMode) {
            $debug = [
                'client_id' => $clientId,
                'private_key' => $privateKey,
                'access_token_response' => $response
            ];
            $this->logger->info($this->serializer->serialize($debug));
        }
    }

    private function generateJWTSignature($header, $claim, $privateKey, $algorithm = 'sha256WithRSAEncryption'): ?string
    {
        try {
            openssl_sign(
                $header . "." . $claim,
                $jwtSig,
                $privateKey,
                $algorithm
            );
            return $this->getEncoded($jwtSig);
        } catch (Exception $exception) {
            $this->logger->error(
                'Something went wrong with the Pay Your Way.',
                [
                    'exception' => (string)$exception,
                ]
            );
            return null;
        }
    }

    private function getAccessTokenRequest($header, $claim, $jwtSig)
    {
        $jwtAssertion = $header . "." . $claim . "." . $jwtSig;

        /**
         * Get the access token calling PYW service
         */

        /** @var AccessTokenRequestInterface $accessTokenRequest */
        $accessTokenRequest = $this->accessTokenRequestFactory->create();
        $accessTokenRequest->setJwt($jwtAssertion);
        $accessTokenRequest->setGrantType(self::GRANT_TYPE);
        $accessTokenRequest->setContentType('application/json');

        $accessTokenEncode = $this->accessTokenLookup->execute($accessTokenRequest);
        return $this->serializer->unserialize($accessTokenEncode);
    }

    private function saveAccessToken($accessTokenDecoded, $merchantId, $sandbox): ?AccessToken
    {
        /** @var AccessToken $accessToken */
        $accessToken = $this->accessTokenFactory->create();
        $accessToken->setAccessToken((string)$accessTokenDecoded['access_token']);
        $accessToken->setTokenType((string)$accessTokenDecoded['token_type']);
        $accessToken->setExp((int)$accessTokenDecoded['exp']);
        $accessToken->setIss((int)$accessTokenDecoded['iss']);
        $accessToken->setMerchantId($merchantId);
        $accessToken->setSandbox($sandbox);
        try {
            $this->resourceModel->save($accessToken);
            return $accessToken;
        } catch (AlreadyExistsException $e) {
            return null;
        }
    }
}
