<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

use Magento\Store\Model\ScopeInterface;

/**
 * Used for managing the Pay Your Way integration config settings
 * @api
 */
interface ConfigInterface
{
    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return bool
     */
    public function isPayYourWayEnabled($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): bool;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return bool
     */
    public function isRefreshTokenProcessEnabled($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): bool;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getEnvironment($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE):string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getClientId($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE):string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getPrivateKey($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE):string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getPaymentConfirmationApiEndpoint(
        $scopeId = null,
        string $scope = ScopeInterface::SCOPE_STORE
    ): string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getPaymentConfirmationUrl(
        $scopeId = null,
        string $scope = ScopeInterface::SCOPE_STORE
    ): string;
}
