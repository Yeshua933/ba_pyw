<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\StoreManagerInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Model\Adminhtml\Source\Environment;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    private ConfigInterface $scopeConfig;

    private StoreManagerInterface $storeManager;

    private GenerateAccessToken $generateAccessToken;

    private RefIdBuilderInterface $refIdBuilder;

    private CheckoutSession $checkoutSession;

    public function __construct(
        ConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        GenerateAccessToken $generateAccessToken,
        RefIdBuilderInterface $refIdBuilder,
        CheckoutSession $checkoutSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->generateAccessToken = $generateAccessToken;
        $this->refIdBuilder = $refIdBuilder;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \JsonException
     */
    public function getConfig(): array
    {
        $quote = $this->checkoutSession->getQuote();
        /**
         * TODO: Change sdkURL depending on sandbox mode config
         */
        $sandboxMode = $this->scopeConfig->getEnvironment() === Environment::ENVIRONMENT_SANDBOX;
        $refId = $this->refIdBuilder->buildRefId(
            $this->scopeConfig->getClientId(),
            $this->generateAccessToken->execute(),
            $this->scopeConfig->getClientId(),
            time(),
            $quote->getId(),
            $quote->getCustomerEmail() ?? '',
            $sandboxMode
        );
        return [
            'payment' => [
                'payyourway' => [
                    'refid' => $refId,
                    'sdkUrl' => $this->scopeConfig->getPaymentSdkApiEndpoint(),
                    'isActive' => $this->scopeConfig->isPayYourWayEnabled(),
                    'clientId' => $this->scopeConfig->getClientId(),
                    'environment' => $this->scopeConfig->getEnvironment(),
                    'currency' => $this->storeManager->getStore()->getCurrentCurrency()->getCode(),
                ]
            ]
        ];
    }
}
