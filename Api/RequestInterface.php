<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Contains a request to lookup a Payment Confirmation
 *
 * @api
 */
interface RequestInterface
{
    /**
     * Get the Channel
     *
     * @return string
     */
    public function getChannel(): string;

    /**
     * Get the Merchant ID
     *
     * @return string
     */
    public function getMerchantId(): string;

    /**
     * Get the Pay Your Way ID
     *
     * @return string
     */
    public function getPywid(): string;

    /**
     * Get the Transaction ID
     *
     * @return string
     */
    public function getTransactionId(): string;

    /**
     * Get the Action type
     *
     * @return string
     */
    public function getActionType(): string;

    /**
     * Get the Transaction type
     *
     * @return string
     */
    public function getTransactionType(): string;

    /**
     * Get the Ref ID
     *
     * @return string
     */
    public function getRefId(): string;

    /**
     * Set the Channel
     *
     * @param String $channel
     * @return RequestInterface
     */
    public function setChannel(string $channel): RequestInterface;

    /**
     * Set the Merchant ID
     *
     * @param String $merchantId
     * @return RequestInterface
     */
    public function setMerchantId(string $merchantId): RequestInterface;

    /**
     * Set the Pay Your Way ID
     *
     * @param String $pywid
     * @return RequestInterface
     */
    public function setPywid(string $pywid): RequestInterface;

    /**
     * Set the Transaction ID
     *
     * @param String $transactionId
     * @return RequestInterface
     */
    public function setTransactionId(string $transactionId): RequestInterface;

    /**
     * Set the Action type
     *
     * @param String $actionType
     * @return RequestInterface
     */
    public function setActionType(string $actionType): RequestInterface;

    /**
     * Set the Transaction type
     *
     * @param String $transactionType
     * @return RequestInterface
     */
    public function setTransactionType(string $transactionType): RequestInterface;

    /**
     * Set the Ref ID
     *
     * @param String $refId
     * @return RequestInterface
     */
    public function setRefId(string $refId): RequestInterface;
}
