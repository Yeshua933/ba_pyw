<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service\UpdateMerchantLookup;

use PayYourWay\Pyw\Api\UpdateMerchantRequestInterface;

class UpdateMerchantRequest implements UpdateMerchantRequestInterface
{
    private string $contentType;
    private string $environment;
    private string $clientId;
    private string $accept;
    private string $refId;
    private string $name;
    private string $email;
    private string $phone;
    private string $address;
    private string $category;
    private array $domains;

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): UpdateMerchantRequestInterface
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function setEnvironment(string $environment): UpdateMerchantRequestInterface
    {
        $this->environment = $environment;
        return $this;
    }

    public function getAccept(): string
    {
        return $this->accept;
    }

    public function setAccept(string $accept): UpdateMerchantRequestInterface
    {
        $this->accept = $accept;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): UpdateMerchantRequestInterface
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getRefId(): string
    {
        return $this->refId;
    }

    public function setRefId(string $refId): UpdateMerchantRequestInterface
    {
        $this->refId = $refId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): UpdateMerchantRequestInterface
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): UpdateMerchantRequestInterface
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): UpdateMerchantRequestInterface
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): UpdateMerchantRequestInterface
    {
        $this->address = $address;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): UpdateMerchantRequestInterface
    {
        $this->category = $category;
        return $this;
    }

    public function getDomains(): array
    {
        return $this->domains;
    }

    public function setDomains(array $domains): UpdateMerchantRequestInterface
    {
        $this->domains = $domains;
        return $this;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => $this->getContentType(),
            "Accept" => $this->getAccept(),
            "channel" => 'PYW_ONLINE',
            "client_id" => $this->getClientId(),
            "refid" => $this->getRefId(),
        ];
    }

    public function getBody(): array
    {
        return [
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'address' => $this->getAddress(),
            'category' => $this->getCategory(),
            'domains' => $this->getDomains(),
        ];
    }
}
