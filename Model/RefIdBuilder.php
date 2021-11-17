<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use function Sodium\crypto_secretbox;
use function Sodium\randombytes_buf;
use const Sodium\CRYPTO_SECRETBOX_KEYBYTES;
use const Sodium\CRYPTO_SECRETBOX_NONCEBYTES;

class RefIdBuilder implements RefIdBuilderInterface
{
    public function buildRefId(
        string $clientId,
        string $accessToken,
        string $requestorId,
        int $timestamp,
        string $transactionId,
        string $userId
    ): string {
        /**
         * Format of refId: client_id~~access_token~~requestorId~~timestamp~~transactionId~~userId
         */
        $refId = $clientId ."~~". $accessToken ."~~". $requestorId
            ."~~". $timestamp ."~~". $transactionId ."~~". $userId;

        $key = randombytes_buf(CRYPTO_SECRETBOX_KEYBYTES);

        $nonce = randombytes_buf(
            CRYPTO_SECRETBOX_NONCEBYTES
        );

        return base64_encode(
            $nonce.
            crypto_secretbox(
                $refId,
                $nonce,
                $key
            )
        );
    }
}
