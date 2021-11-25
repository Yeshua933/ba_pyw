<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service\OnboardingLookup;

use PayYourWay\Pyw\Api\OnboardingRequestInterface;

class OnboardingRequest implements OnboardingRequestInterface
{
    private string $contentType;
    private string $bearer;
    private string $clientName;
    private string $clientEmail;
    private string $clientId;
    private string $phoneNumber;
    private string $publicKey;
    private string $environment;

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): OnboardingRequestInterface
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getBearer(): string
    {
        return $this->bearer;
    }

    public function setBearer(string $bearer): OnboardingRequestInterface
    {
        $this->bearer = $bearer;
        return $this;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => $this->getContentType()
        ];
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): OnboardingRequestInterface
    {
        $this->clientName = $clientName;
        return $this;
    }

    public function getClientEmail(): string
    {
        return $this->clientEmail;
    }

    public function setClientEmail(string $clientEmail): OnboardingRequestInterface
    {
        $this->clientEmail = $clientEmail;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): OnboardingRequestInterface
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): OnboardingRequestInterface
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $publicKey): OnboardingRequestInterface
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function setEnvironment(string $environment): OnboardingRequestInterface
    {
        $this->environment = $environment;
        return $this;
    }

    public function getBody(): array
    {
        return [
            'clientName' => $this->getClientName(),
            'clientEmail' => $this->getClientEmail(),
            'clientId' => $this->getClientId(),
            'phoneNumber' => $this->getPhoneNumber(),
            'password' => '',
            'noPasswordFlag' => true,
            'secretCodeFlag' => true,
            'publicKey' => $this->getPublicKey()
        ];
    }
}
