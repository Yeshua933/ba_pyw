<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;


/**
 * Used for creating/renewing the Pay Your Way access token
 * @api
 */
class GenerateAccessToken implements GenerateAccessTokenInterface {

    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function execute(): string
    {
        //TODO add the generate process; load store configuration
        $header = $this->base64url_encode(json_encode(array('typ' => 'JWT', 'alg' => 'RSA'), JSON_THROW_ON_ERROR));
        $client_id = $this->config->getClientId();
        $privateKey = $this->config->getPrivateKey();
        $aud = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX ?
            "https://oauth.uat.telluride.transformco.com/oauthAS/service/oAuth/token.json" :
            "https://oauth.telluride.transformco.com/oauthAS/service/oAuth/token.json";

        $claim = $this->base64url_encode(
            json_encode(array(
                "iss" => $client_id,
                "scope" => "",
                "aud" => $aud,
                "exp" => round(microtime(true)*1000) + 7200000 ,
                "iat" => round(microtime(true)*1000)
            ), JSON_THROW_ON_ERROR));

        openssl_sign(
            $header.".".$claim,
            $jwtSig,
            $privateKey,
            "sha256WithRSAEncryption"
        );
        $jwtSig = $this->base64url_encode($jwtSig);

        $jwtAssertion = $header.".".$claim.".".$jwtSig;

        return $jwtAssertion;
    }

    public function base64url_encode($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
