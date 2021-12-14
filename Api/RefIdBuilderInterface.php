<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Interface for building RefId
 *
 * @api
 */
interface RefIdBuilderInterface
{
    /**
     * @param string $clientId
     * @param string $accessToken
     * @param string $requestorId
     * @param int $timestamp
     * @param string $transactionId
     * @param string|null $userId
     * @param bool $sandbox
     * @return string|null
     */
    public function buildRefId(
        string $clientId,
        string $accessToken,
        string $requestorId,
        int $timestamp,
        string $transactionId,
        string $userId = '',
        bool $sandbox = false,
        string $secretCode = ''
    ): ?string;
}
