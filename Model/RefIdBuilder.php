<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;

class RefIdBuilder implements RefIdBuilderInterface
{
    private ConfigInterface $config;

    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function buildRefId(
        string $clientId,
        string $accessToken,
        string $requestorId,
        int $timestamp,
        string $transactionId,
        string $userId = '',
        bool $sandbox = false,
        string $secretCode = ''
    ): ?string {

        if ($sandbox) {
            $requestorId = 'PSYW';
        }
        /**
         * Format of refId: client_id~~access_token~~requestorId~~timestamp~~transactionId~~userId
         */
        $refId = $clientId ."~~". $accessToken ."~~". $requestorId
            ."~~". $timestamp ."~~". $transactionId ."~~". $userId;

        return $this->getEncrypt($refId, $secretCode);
    }

    private function getEncrypt(string $refId, string $secretCode = ''): ?string
    {
        if ($secretCode !== '') {
            return $this->encrypt($refId, $secretCode);
        }
        $secretCode = $this->config->getSecretKey();
        if ($secretCode !== null) {
            return $this->encrypt($refId, $secretCode);
        }
        return null;
    }

    public function encrypt(string $value, string $key)
    {
        return openssl_encrypt($value, 'AES-128-ECB', $key);
    }
}
