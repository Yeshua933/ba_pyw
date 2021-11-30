<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Contains a request to lookup a Merchant
 *
 * @api
 */
interface UpdateMerchantRequestInterface
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
     * @return UpdateMerchantRequestInterface
     */
    public function setContentType(string $contentType): UpdateMerchantRequestInterface;

    /**
     * Return the string
     *
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * Set the ContentType
     *
     * @param string $environment
     * @return UpdateMerchantRequestInterface
     */
    public function setEnvironment(string $environment): UpdateMerchantRequestInterface;

    /**
     * Return the string
     *
     * @return string
     */
    public function getAccept(): string;

    /**
     * Set the Accept
     *
     * @param string $accept
     * @return UpdateMerchantRequestInterface
     */
    public function setAccept(string $accept): UpdateMerchantRequestInterface;

    /**
     * Return the string
     *
     * @return string
     */
    public function getClientId(): string;

    /**
     * Set the ClientId
     *
     * @param string $clientId
     * @return UpdateMerchantRequestInterface
     */
    public function setClientId(string $clientId): UpdateMerchantRequestInterface;

    /**
     * Return the string
     *
     * @return string
     */
    public function getRefId(): string;

    /**
     * Set the RefId
     *
     * @param string $refId
     * @return UpdateMerchantRequestInterface
     */
    public function setRefId(string $refId): UpdateMerchantRequestInterface;

    /**
     * Return the Name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set the Name
     *
     * @param string $name
     * @return UpdateMerchantRequestInterface
     */
    public function setName(string $name): UpdateMerchantRequestInterface;

    /**
     * Return the Email
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Set the Email
     *
     * @param string $email
     * @return UpdateMerchantRequestInterface
     */
    public function setEmail(string $email): UpdateMerchantRequestInterface;

    /**
     * Return the Phone
     *
     * @return string
     */
    public function getPhone(): string;

    /**
     * Set the Phone
     *
     * @param string $phone
     * @return UpdateMerchantRequestInterface
     */
    public function setPhone(string $phone): UpdateMerchantRequestInterface;

    /**
     * Return the Address
     *
     * @return string
     */
    public function getAddress(): string;

    /**
     * Set the Address
     *
     * @param string $address
     * @return UpdateMerchantRequestInterface
     */
    public function setAddress(string $address): UpdateMerchantRequestInterface;

    /**
     * Return the Category
     *
     * @return string
     */
    public function getCategory(): string;

    /**
     * Set the Category
     *
     * @param string $category
     * @return UpdateMerchantRequestInterface
     */
    public function setCategory(string $category): UpdateMerchantRequestInterface;

    /**
     * Return the Domains
     *
     * @return array
     */
    public function getDomains(): array;

    /**
     * Set the Domains
     *
     * @param array $domains
     * @return UpdateMerchantRequestInterface
     */
    public function setDomains(array $domains): UpdateMerchantRequestInterface;

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
}
