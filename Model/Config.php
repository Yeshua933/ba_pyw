<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PayYourWay\Pyw\Api\ConfigInterface;

/**
 * Configuration retrieval tool
 */
class Config implements ConfigInterface
{
    public const CONFIG_XML_PATH_ENABLE_PAY_YOUR_WAY = 'payment/payyourway/active';
    public const CONFIG_XML_PATH_ENABLE_REFRESH_TOKEN_PROCESS = 'payment/payyourway/enable_refresh_token_process';

    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
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
}
