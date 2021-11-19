<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Contains a request to lookup a Payment Return
 *
 * @api
 */
interface PaymentReturnRequestInterface
{
    /**
     * Get the Content-Type
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Get the Accept
     *
     * @return string
     */
    public function getAccept(): string;

    /**
     * Get the Channel
     *
     * @return string
     */
    public function getChannel(): string;

    /**
     * Get the Client ID
     *
     * @return string
     */
    public function getClientId(): string;

    /**
     * Get the Transaction ID
     *
     * @return string
     */
    public function getTransactionId(): string;

    /**
     * Get the Ref ID
     *
     * @return string
     */
    public function getRefId(): string;

    /**
     * Return the headers
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Set the Content-Type
     *
     * @param String $contentType
     * @return PaymentReturnRequestInterface
     */
    public function setContentType(string $contentType): PaymentReturnRequestInterface;

    /**
     * Set the Accept
     *
     * @param String $accept
     * @return PaymentReturnRequestInterface
     */
    public function setAccept(string $accept): PaymentReturnRequestInterface;

    /**
     * Set the Channel
     *
     * @param String $channel
     * @return PaymentReturnRequestInterface
     */
    public function setChannel(string $channel): PaymentReturnRequestInterface;

    /**
     * Set the Client ID
     *
     * @param String $clientId
     * @return PaymentReturnRequestInterface
     */
    public function setClientId(string $clientId): PaymentReturnRequestInterface;

    /**
     * Set the Transaction ID
     *
     * @param String $transactionId
     * @return PaymentReturnRequestInterface
     */
    public function setTransactionId(string $transactionId): PaymentReturnRequestInterface;

    /**
     * Set the Ref ID
     *
     * @param String $refId
     * @return PaymentReturnRequestInterface
     */
    public function setRefId(string $refId): PaymentReturnRequestInterface;
}
