<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Cron;

use Magento\Framework\Encryption\Encryptor;
use PayYourWay\Pyw\Model\Config;

class RefreshToken
{
    private Config $config;
    private Encryptor $encryptor;

    public function __construct(
        Config $config,
        Encryptor $encryptor
    ) {
        $this->config = $config;
        $this->encryptor= $encryptor;
    }

    /**
     * Refresh and generate a token.
     * @todo: This is a work in progress. We need to separate responsibilities
     */
    public function execute()
    {
        /**
         * @todo: Add a condition to check if is token expired ( > 15 minutes )
         */
        if ($this->config->isPayYourWayEnabled() && $this->config->isRefreshTokenProcessEnabled())
        {
            $jwt = $this->generateJwt();

            $accessToken = $this->getAccessToken($jwt);

            /**
             * @todo: Save the access token into the store configuration
             */
        }
    }

    /**
     * This is a work in progress
     */
    private function generateJwt(): string
    {
        $header = '{
                "alg":"RSA",
                "typ":"JWT"
            }';
        $encodedHeader = base64_encode($header);

        /**
         * @todo: Generate a client Id, exp and iat
         */
        $claimSet = '{
                "iss":"Client Id",
                "scope":"",
                "aud":"https://oauth.uat.telluride.transformco.com/oauthAS/service/oAuth/token.json",
                "exp":"1328554385232",
                "iat":"1328550785227"
            }';
        $encodedClaim = base64_encode($claimSet);

        /**
         * The input for the signature is the byte array of encoded header and claim content:
         * Using .NET the input content will for signature algorithm will be:
         * byte[] inputforSignature = System.Text.Encoding.UTF8.GetBytes({Base64url encoded header}.{Base64url encoded claim set}
         *
         * In PHP: https://stackoverflow.com/questions/885597/string-to-byte-array-in-php
         */
        $inputForSignature = array_values(unpack("C*", "{$encodedHeader}.{$encodedClaim}"));

        $encodedSignature = base64_encode($this->encryptor->getHash($inputForSignature, false, 1));

        return "{$encodedHeader}.{$encodedClaim}.{$encodedSignature}";
    }

    /**
     * This is a work in progress
     */
    private function getAccessToken($jwt): string
    {
        /**
         * @todo: Make REST Request to OAuth server URL
         */
        $accessToken = '';

        return $accessToken;
    }
}
