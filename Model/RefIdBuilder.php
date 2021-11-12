<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use PayYourWay\Pyw\Api\RefIdBuilderInterface;

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

        $key = \Sodium\randombytes_buf(\Sodium\CRYPTO_SECRETBOX_KEYBYTES);

        $nonce = \Sodium\randombytes_buf(
            \Sodium\CRYPTO_SECRETBOX_NONCEBYTES
        );

        return base64_encode(
            $nonce.
            \Sodium\crypto_secretbox(
                $refId,
                $nonce,
                $key
            )
        );
    }
}
