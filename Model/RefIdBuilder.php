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
    private const REF_ID_QA = 'oC4EbwWObOxb8FJtv8aWdaMhkYq3G7Tj6dP5kw1W+NQB4/XJH/pcbHcWVOw2IcO0HN4sq1Avg7Aq9y9XfiTiSbrZGxmY3BFJoiVWnFdGAFFK7xHO1dMllWbN+C6Fk7gOl4ofFMPZmtMi/txc7Xme25sdxAjR2NWkx59hT5J9hFmDeKFjT1DokvKUkilAo6IQr1kbtU5CZSeL56m9BfTQfFY0C3VF0FHAWV0d+lDE4RfWa5hp4TVLOVYIeLDwSB8cIu9pmfyE4aLpS+eO8bSHrE+D6KQleNNbrLySo8KbUOuer0D/dMZYgvP36Kv9HtS9FFeOOVAiEstYByvAgiy2Cw=='; //phpcs:ignore

    public function buildRefId(
        string $clientId,
        string $accessToken,
        string $requestorId,
        int $timestamp,
        string $transactionId,
        string $userId = '',
        bool $sandbox = false
    ): string {

        if ($sandbox) {
            return self::REF_ID_QA;
        }
        /**
         * Format of refId: client_id~~access_token~~requestorId~~timestamp~~transactionId~~userId
         */
        $refId = $clientId ."~~". $accessToken ."~~". $requestorId
            ."~~". $timestamp ."~~". $transactionId ."~~". $userId; //@todo add validation to userid

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
