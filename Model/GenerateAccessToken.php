<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Serialize\SerializerInterface;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Api\AccessTokenRequestInterfaceFactory;
use PayYourWay\Pyw\Api\AccessTokenLookupInterface;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken as ResourceModel;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken\Collection;
use PayYourWay\Pyw\Model\AccessTokenFactory;
use PayYourWay\Pyw\Model\Adminhtml\Config\Source\Environment;

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

    public function __construct(
        Config $config,
        AccessTokenRequestInterfaceFactory $accessTokenRequestFactory,
        AccessTokenLookupInterface $accessTokenLookup,
        AccessTokenFactory $accessTokenFactory,
        ResourceModel $resourceModel,
        SerializerInterface $serializer,
        Collection $collection
    ) {
        $this->config = $config;
        $this->accessTokenRequestFactory = $accessTokenRequestFactory;
        $this->accessTokenLookup = $accessTokenLookup;
        $this->resourceModel = $resourceModel;
        $this->accessTokenFactory = $accessTokenFactory;
        $this->serializer = $serializer;
        $this->collection = $collection;
    }

    /**
     * @inheritdoc
     */
    public function execute(): ?string
    {
        //TODO add the generate process; load store configuration
        $header = $this->getEncoded(json_encode(['typ' => 'JWT', 'alg' => 'RSA'], JSON_THROW_ON_ERROR));
        $client_id = $this->config->getClientId();
        $privateKey = $this->config->getPrivateKey();

        if ($client_id === null || $privateKey === null) {
            return null;
        }

        $currentAccessToken = $this->collection->getFirstItem();

        if ($currentAccessToken->getId() != null) {

            $timeNow = (int) microtime(true) * 1000;
            $expirationTime = (int) $currentAccessToken->getExp();

            $difference = $expirationTime - $timeNow;

            /**
             * If the difference is greater or equal than 15 minutes
             */
            if ($difference >= self::THRESHOLD) {
                return $currentAccessToken->getAccessToken();
            }

            try {
                $this->resourceModel->delete($currentAccessToken);
            } catch (\Exception $e) {
                return null;
            }
        }

        $aud = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX ?
            self::OAUTH_UAT :
            self::OAUTH_PRD;

        $claim = $this->getEncoded(
            json_encode([
                "iss" => $client_id,
                "scope" => "",
                "aud" => $aud,
                "exp" => round(microtime(true)*1000) + 7200000 ,
                "iat" => round(microtime(true)*1000)
            ], JSON_THROW_ON_ERROR)
        );

        openssl_sign(
            $header.".".$claim,
            $jwtSig,
            $privateKey,
            "sha256WithRSAEncryption"
        );
        $jwtSig = $this->getEncoded($jwtSig);

        $jwtAssertion = $header.".".$claim.".".$jwtSig;

        /**
         * Get the access token calling PYW service
         */

        /** @var \PayYourWay\Pyw\Api\AccessTokenRequestInterface $accessTokenRequest */
        $accessTokenRequest = $this->accessTokenRequestFactory->create();
        $accessTokenRequest->setJwt($jwtAssertion);
        $accessTokenRequest->setGrantType(self::GRANT_TYPE);
        $accessTokenRequest->setContentType('application/json');

        $accessTokenEncode = $this->accessTokenLookup->execute($accessTokenRequest);
        $accessTokenDecode = $this->serializer->unserialize($accessTokenEncode);

        /** @var \PayYourWay\Pyw\Model\AccessToken $accessToken */
        $accessToken = $this->accessTokenFactory->create();
        $accessToken->setAccessToken((string)$accessTokenDecode['access_token']);
        $accessToken->setTokenType((string)$accessTokenDecode['token_type']);
        $accessToken->setExp((int)$accessTokenDecode['exp']);
        $accessToken->setIss((int)$accessTokenDecode['iss']);
        try {
            $this->resourceModel->save($accessToken);
        } catch (AlreadyExistsException $e) {
            return null;
        }

        return $accessToken->getAccessToken();
    }

    private function getEncoded($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
