<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use PayYourWay\Pyw\Api\ConfigInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    private const REF_ID_QA = 'oC4EbwWObOxb8FJtv8aWdaMhkYq3G7Tj6dP5kw1W+NQB4/XJH/pcbHcWVOw2IcO0HN4sq1Avg7Aq9y9XfiTiSbrZGxmY3BFJoiVWnFdGAFFK7xHO1dMllWbN+C6Fk7gOl4ofFMPZmtMi/txc7Xme25sdxAjR2NWkx59hT5J9hFmDeKFjT1DokvKUkilAo6IQr1kbtU5CZSeL56m9BfTQfFY0C3VF0FHAWV0d+lDE4RfWa5hp4TVLOVYIeLDwSB8cIu9pmfyE4aLpS+eO8bSHrE+D6KQleNNbrLySo8KbUOuer0D/dMZYgvP36Kv9HtS9FFeOOVAiEstYByvAgiy2Cw=='; //phpcs:ignore

    /** @var ConfigInterface */
    private ConfigInterface $scopeConfig;

    /** @var StoreManagerInterface */
    private StoreManagerInterface $storeManager;


    public function __construct(ConfigInterface $scopeConfig,StoreManagerInterface $storeManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig(): array
    {
        /**
         * TODO: Change sdkURL depending on sandbox mode config
         */
        return [
            'payment' => [
                'payyourway' => [
                    'refid' => self::REF_ID_QA,
                    'sdkUrl' => $this->scopeConfig->getPaymentSdkApiEndpoint(),
                    'isActive' => $this->scopeConfig->isPayYourWayEnabled(),
                    'clientId' => $this->scopeConfig->getClientId(),
                    'environment' => $this->scopeConfig->getEnvironment(),
                    'currency' => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
                ]
            ]
        ];
    }
}
