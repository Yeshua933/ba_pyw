<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service\AccessTokenLookup;

use PayYourWay\Pyw\Api\AccessTokenRequestInterface;

class AccessTokenRequest implements AccessTokenRequestInterface
{
    private string $contentType;
    private string $jwt;
    private string $grantType;

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): AccessTokenRequestInterface
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => $this->getContentType(),
        ];
    }

    public function getJwt(): string
    {
        return $this->jwt;
    }

    public function setJwt(string $jwt): AccessTokenRequestInterface
    {
        $this->jwt = $jwt;
        return $this;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function setGrantType(string $grantType): AccessTokenRequestInterface
    {
        $this->grantType = $grantType;
        return $this;
    }

    public function getBody(): array
    {
        return [
            'assertion' => $this->getJwt(),
            'grant_type' => $this->getGrantType()
        ];
    }
}
