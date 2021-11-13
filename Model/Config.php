<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface as ResourceConfigInterface;
use PayYourWay\Pyw\Api\ConfigInterface as PywConfigInterface;
use PayYourWay\Pyw\Model\Adminhtml\Source\Environment;

/**
 * Configuration retrieval tool
 */
class Config implements PywConfigInterface
{
    public const CONFIG_XML_PATH_ENABLE_PAY_YOUR_WAY = 'payment/payyourway/active';
    public const CONFIG_XML_PATH_ENABLE_REFRESH_TOKEN_PROCESS = 'payment/payyourway/enable_refresh_token_process';
    public const CONFIG_XML_PATH_ENVIRONMENT = 'payment/payyourway/environment';
    public const CONFIG_XML_PATH_ClIENT_ID_PR = 'payment/payyourway/client_id_pr';
    public const CONFIG_XML_PATH_ClIENT_ID_SB = 'payment/payyourway/client_id_sb';
    public const CONFIG_XML_PATH_PRIVATE_KEY_PR = 'payment/payyourway/private_key_pr';
    public const CONFIG_XML_PATH_PRIVATE_KEY_SB = 'payment/payyourway/private_key_sb';
    public const CONFIG_XML_PATH_ACCESS_TOKEN_PR = 'payment/payyourway/access_token_pr';
    public const CONFIG_XML_PATH_ACCESS_TOKEN_SB = 'payment/payyourway/access_token_sb';

    private ScopeConfigInterface $scopeConfig;
    private ResourceConfigInterface $resourceConfigInterface;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConfigInterface $resourceConfigInterface
     */
    public function __construct(ScopeConfigInterface $scopeConfig,ResourceConfigInterface $resourceConfigInterface)
    {
        $this->scopeConfig = $scopeConfig;
        $this->resourceConfigInterface = $resourceConfigInterface;
    }

    /**
     * Determine if Pay Your Way has been enabled
     *
     * @param string|null $scopeId
     * @param string $scope
     * @return bool
     */
    public function isPayYourWayEnabled($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_XML_PATH_ENABLE_PAY_YOUR_WAY, $scope, $scopeId);
    }

    /**
     * Determine if Pay Your Way has been enabled
     *
     * @param string|null $scopeId
     * @param string $scope
     * @return bool
     */
    public function isRefreshTokenProcessEnabled($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_XML_PATH_ENABLE_REFRESH_TOKEN_PROCESS, $scope, $scopeId);
    }

    /**
     * Get environment
     *
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getEnvironment($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_XML_PATH_ENVIRONMENT, $scope, $scopeId);
    }

    /**
     * Get Client Id
     *
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getClientId($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE):string
    {
        if ($this->getEnvironment() === Environment::ENVIRONMENT_SANDBOX) {
            return $this->scopeConfig->getValue(
                self::CONFIG_XML_PATH_ClIENT_ID_SB,
                $scope, $scopeId
            );
        }
        return $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_ClIENT_ID_PR,
            $scope, $scopeId
        );
    }

    /**
     * Get Private Key
     *
     * @param string|null $scopeId
     * @param string $scope
     * @return string
     */
    public function getPrivateKey($scopeId = null, string $scope = ScopeInterface::SCOPE_STORE):string
    {
        if ($this->getEnvironment() === Environment::ENVIRONMENT_SANDBOX) {

            $privateKey = $this->scopeConfig->getValue(
                self::CONFIG_XML_PATH_PRIVATE_KEY_SB,
                $scope, $scopeId
            );
            $privateKey = str_replace(array("-----BEGIN PRIVATE KEY-----","-----END PRIVATE KEY-----","\r\n", "\n", "\r"," "), '', $privateKey);
            $privateKey = chunk_split($privateKey, 64, "\n");
            return "-----BEGIN RSA PRIVATE KEY-----\n$privateKey-----END RSA PRIVATE KEY-----\n";
        }

        $privateKey = $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_PRIVATE_KEY_PR,
            $scope, $scopeId
        );
        $privateKey = str_replace(array("-----BEGIN PRIVATE KEY-----","-----END PRIVATE KEY-----","\r\n", "\n", "\r"," "), '', $privateKey);
        $privateKey = chunk_split($privateKey, 64, "\n");
        return "-----BEGIN RSA PRIVATE KEY-----\n$privateKey-----END RSA PRIVATE KEY-----\n";
    }

    /**
     * Save access token value
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return \Magento\Config\Model\ResourceModel\Config
     */
    public function saveAccessToken( string $value,string $path=self::CONFIG_XML_PATH_ACCESS_TOKEN_SB, string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, int $scopeId = 0):ResourceConfigInterface
    {
        if ($this->getEnvironment() === Environment::ENVIRONMENT_SANDBOX) {
            $path = self::CONFIG_XML_PATH_ACCESS_TOKEN_SB;
            return $this->resourceConfigInterface->saveConfig($path, $value, $scope,$scopeId);
        }

        $path = self::CONFIG_XML_PATH_ACCESS_TOKEN_PR;
        return $this->resourceConfigInterface->saveConfig($path, $value, $scope,$scopeId);
    }



}
