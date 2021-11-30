<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Contains a request to lookup an AccessToken
 *
 * @api
 */
interface AccessTokenRequestInterface
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
     * @return AccessTokenRequestInterface
     */
    public function setContentType(string $contentType): AccessTokenRequestInterface;

    /**
     * Return the headers
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Get the Jwt
     *
     * @return string
     */
    public function getJwt(): string;

    /**
     * Set the Jwt
     *
     * @param string $jwt
     * @return AccessTokenRequestInterface
     */
    public function setJwt(string $jwt): AccessTokenRequestInterface;

    /**
     * Get the GrantType
     *
     * @return string
     */
    public function getGrantType(): string;

    /**
     * Set the GrantType
     *
     * @param string $grantType
     * @return AccessTokenRequestInterface
     */
    public function setGrantType(string $grantType): AccessTokenRequestInterface;

    /**
     * Return the body
     *
     * @return array
     */
    public function getBody(): array;
}
