<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service\PaymentReturnLookup;

use PayYourWay\Pyw\Api\PaymentReturnRequestInterface;

class PaymentReturnRequest implements PaymentReturnRequestInterface
{
    private string $contentType;
    private string $accept;
    private string $channel;
    private string $clientId;
    private string $transactionId;
    private string $refId;

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getAccept(): string
    {
        return $this->accept;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getRefId(): string
    {
        return $this->refId;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => $this->getContentType(),
            'Accept' => $this->getAccept(),
            'channel' => $this->getChannel(),
            'client_id' => $this->getClientId(),
            'transactionId' => $this->getTransactionId(),
            'refId' => $this->getRefId()
        ];
    }

    public function setContentType(string $contentType): PaymentReturnRequestInterface
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function setAccept(string $accept): PaymentReturnRequestInterface
    {
        $this->accept = $accept;
        return $this;
    }

    public function setChannel(string $channel): PaymentReturnRequestInterface
    {
        $this->channel = $channel;
        return $this;
    }

    public function setClientId(string $clientId): PaymentReturnRequestInterface
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function setTransactionId(string $transactionId): PaymentReturnRequestInterface
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function setRefId(string $refId): PaymentReturnRequestInterface
    {
        $this->refId = $refId;
        return $this;
    }
}
