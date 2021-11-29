<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface as ResourceConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Used for managing the Pay Your Way integration config settings
 *
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
    public function getEnvironment($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): ?string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getClientId($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): ?string;

    /**
     * Get Access Token
     *
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getAccessToken($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): ?string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getPrivateKey($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): ?string;

    /**
     * Save private key
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return Config
     */
    public function savePrivateKey(
        string $value,
        string $path,
        string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        int    $scopeId = 0
    ): ResourceConfigInterface;

    /**
     * Save config value to the storage resource
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return ResourceConfigInterface $ResourceConfigInterface
     */
    public function saveAccessToken(
        string $value,
        string $path,
        string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        int    $scopeId = 0
    ): ResourceConfigInterface;

    /**
     * Save config value to the storage resource
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return ResourceConfigInterface $ResourceConfigInterface
     */
    public function saveSecretKey(
        string $value,
        string $path,
        string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        int    $scopeId = 0
    ): ResourceConfigInterface;

    /**
     * Save access Client ID
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return \Magento\Config\Model\ResourceModel\Config
     */
    public function saveClientId(
        string $value,
        string $path,
        string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        int $scopeId = 0
    ):ResourceConfigInterface;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getPaymentConfirmationApiEndpoint(
        $scopeId = null,
        string $scope = ScopeInterface::SCOPE_STORE
    ): ?string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getPaymentConfirmationUrl(
        $scopeId = null,
        string $scope = ScopeInterface::SCOPE_STORE
    ): ?string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getPaymentReturnApiEndpoint(
        $scopeId = null,
        string $scope = ScopeInterface::SCOPE_STORE
    ): ?string;

    /**
     * @return string|null
     */
    public function getPaymentSdkApiEndpoint(): string;

    /**
     * @param string|null $scopeId
     * @param string $scope
     * @return bool
     */
    public function isDebugMode($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): bool;

    /**
     * Get Merchant Id/ Client Id
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getMerchantName(string $scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): ?string;
}
