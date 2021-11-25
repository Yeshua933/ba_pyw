<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Contains a request to execute the Onboarding
 *
 * @api
 */
interface OnboardingRequestInterface
{
    /**
     * Return the string
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * Set the ContentType
     *
     * @param string $contentType
     * @return OnboardingRequestInterface
     */
    public function setContentType(string $contentType): OnboardingRequestInterface;

    /**
     * Return the headers
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Return the body
     *
     * @return array
     */
    public function getBody(): array;

    /**
     * Return the Bearer
     *
     * @return string
     */
    public function getBearer(): string;

    /**
     * Set the Bearer
     *
     * @param string $bearer
     * @return OnboardingRequestInterface
     */
    public function setBearer(string $bearer): OnboardingRequestInterface;

    /**
     * Return the ClientName
     *
     * @return string
     */
    public function getClientName(): string;

    /**
     * Set the ClientName
     *
     * @param string $clientName
     * @return OnboardingRequestInterface
     */
    public function setClientName(string $clientName): OnboardingRequestInterface;

    /**
     * Return the ClientEmail
     *
     * @return string
     */
    public function getClientEmail(): string;

    /**
     * Set the Bearer
     *
     * @param string $clientEmail
     * @return OnboardingRequestInterface
     */
    public function setClientEmail(string $clientEmail): OnboardingRequestInterface;

    /**
     * Return the ClientId
     *
     * @return string
     */
    public function getClientId(): string;

    /**
     * Set the Bearer
     *
     * @param string $clientId
     * @return OnboardingRequestInterface
     */
    public function setClientId(string $clientId): OnboardingRequestInterface;

    /**
     * Return the PhoneNumber
     *
     * @return string
     */
    public function getPhoneNumber(): string;

    /**
     * Set the PhoneNumber
     *
     * @param string $phoneNumber
     * @return OnboardingRequestInterface
     */
    public function setPhoneNumber(string $phoneNumber): OnboardingRequestInterface;

    /**
     * Return the PublicKey
     *
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * Set the PublicKey
     *
     * @param string $publicKey
     * @return OnboardingRequestInterface
     */
    public function setPublicKey(string $publicKey): OnboardingRequestInterface;

    /**
     * Return the Environment
     *
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * Set the PublicKey
     *
     * @param string $environment
     * @return OnboardingRequestInterface
     */
    public function setEnvironment(string $environment): OnboardingRequestInterface;
}
