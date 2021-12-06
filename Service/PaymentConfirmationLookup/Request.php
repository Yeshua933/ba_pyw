<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service\PaymentConfirmationLookup;

use PayYourWay\Pyw\Api\RequestInterface;

class Request implements RequestInterface
{
    private string $channel;
    private string $merchantId;
    private string $pywid;
    private string $transactionId;
    private string $actionType;
    private string $transactionType;
    private string $refId;

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getPywid(): string
    {
        return $this->pywid;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function getRefId(): string
    {
        return $this->refId;
    }

    public function setChannel(string $channel): RequestInterface
    {
        $this->channel = $channel;
        return $this;
    }

    public function setMerchantId(string $merchantId): RequestInterface
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    public function setPywid(string $pywid): RequestInterface
    {
        $this->pywid = $pywid;
        return $this;
    }

    public function setTransactionId(string $transactionId): RequestInterface
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function setActionType(string $actionType): RequestInterface
    {
        $this->actionType = $actionType;
        return $this;
    }

    public function setTransactionType(string $transactionType): RequestInterface
    {
        $this->transactionType = $transactionType;
        return $this;
    }

    public function setRefId(string $refId): RequestInterface
    {
        $this->refId = $refId;
        return $this;
    }

    public function getHeaders(): array
    {
        return [
            "Accept" => "application/json",
            "channel" => $this->getChannel(),
            "platform" => 'PYW',
            "merchantClientId" => $this->getMerchantId(),
            "pywid" => $this->getPywid(),
            "transactionId" => $this->getTransactionId(),
            "actionType" => $this->getActionType(),
            "transactionType" => $this->getTransactionType(),
            "refid" => $this->getRefId()
        ];
    }
}
