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
     * Return the Auth Code
     *
     * @return string
     */
    public function getAuthCode(): string;

    /**
     * Return the Return Amount
     *
     * @return string
     */
    public function getReturnAmount(): string;

    /**
     * Return the Return Payments
     *
     * @return array
     */
    public function getReturnPayments(): ?array;

    /**
     * Return the body
     *
     * @return array
     */
    public function getBody(): array;

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

    /**
     * Set the Auth Code
     *
     * @param String $authCode
     * @return PaymentReturnRequestInterface
     */
    public function setAuthCode(string $authCode): PaymentReturnRequestInterface;

    /**
     * Set the Return Amount
     *
     * @param String $returnAmount
     * @return PaymentReturnRequestInterface
     */
    public function setReturnAmount(string $returnAmount): PaymentReturnRequestInterface;

    /**
     * Set the Return Payments
     *
     * @param null|array $returnPayments
     * @return PaymentReturnRequestInterface
     */
    public function setReturnPayments(?array $returnPayments): PaymentReturnRequestInterface;
}
