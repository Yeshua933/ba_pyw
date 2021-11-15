<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use PayYourWay\Pyw\Api\ConfigInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $scopeConfig;

    /**
     * @param ConfigInterface $scopeConfig
     */
    public function __construct(ConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                'payyourway' => [
                    'refid' => 'oC4EbwWObOxb8FJtv8aWdaMhkYq3G7Tj6dP5kw1W+NQB4/XJH/pcbHcWVOw2IcO0HN4sq1Avg7Aq9y9XfiTiSbrZGxmY3BFJoiVWnFdGAFFK7xHO1dMllWbN+C6Fk7gOl4ofFMPZmtMi/txc7Xme25sdxAjR2NWkx59hT5J9hFmDeKFjT1DokvKUkilAo6IQr1kbtU5CZSeL56m9BfTQfFY0C3VF0FHAWV0d+lDE4RfWa5hp4TVLOVYIeLDwSB8cIu9pmfyE4aLpS+eO8bSHrE+D6KQleNNbrLySo8KbUOuer0D/dMZYgvP36Kv9HtS9FFeOOVAiEstYByvAgiy2Cw==',
                    'sdkUrl' => 'https://pywweb.uat.telluride.shopyourway.com/pyw_library/scripts/pywscript',
                    'isActive' => $this->scopeConfig->isPayYourWayEnabled(),
                    'clientId' => $this->scopeConfig->getClientId(),
                    'environment' => $this->scopeConfig->getEnvironment(),
                    'currency' => 'US Dollar'
                ]
            ]
        ];
    }
}
