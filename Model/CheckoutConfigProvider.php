<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Model\Adminhtml\Source\Environment;
use Psr\Log\LoggerInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    private ConfigInterface $scopeConfig;
    private StoreManagerInterface $storeManager;
    private GenerateAccessToken $generateAccessToken;
    private RefIdBuilderInterface $refIdBuilder;
    private CheckoutSession $checkoutSession;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(
        ConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        GenerateAccessToken $generateAccessToken,
        RefIdBuilderInterface $refIdBuilder,
        CheckoutSession $checkoutSession,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->generateAccessToken = $generateAccessToken;
        $this->refIdBuilder = $refIdBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->logger = $logger;
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
        $sandboxMode = $this->scopeConfig->getEnvironment() === Environment::ENVIRONMENT_SANDBOX;
        $accessToken =  (string) $this->generateAccessToken->execute();
        $quoteId = (string) $quote->getId();
        $customerEmail = $quote->getCustomerEmail() ?? '';

        $refId = $this->getRefId($accessToken, $quoteId, $customerEmail, $sandboxMode);

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

    /**
     * Generates RefId
     *
     * @param string $accessToken
     * @param string $quoteId
     * @param string $customerEmail
     * @param bool $sandboxMode
     * @return string
     */
    private function getRefId(
        string $accessToken,
        string $quoteId,
        string $customerEmail = '',
        bool $sandboxMode = false
    ): string {
        $refId = $this->refIdBuilder->buildRefId(
            $this->scopeConfig->getClientId(),
            $accessToken,
            $this->scopeConfig->getClientId(),
            time(),
            $quoteId,
            $customerEmail,
            $sandboxMode
        );

        if ($this->scopeConfig->isDebugMode()) {
            $debug = [
                'client_id' => $this->scopeConfig->getClientId(),
                'access_token' => $accessToken,
                'requestor_id' => $this->scopeConfig->getClientId(),
                'timestamp' => time(),
                'transaction_id' => $quoteId,
                'user_id' => $customerEmail ?? '',
                'sandbox_mode'=> $sandboxMode,
                'ref_id' => $refId
            ];
            $this->logger->info($this->serializer->serialize($debug));
        }

        return $refId;
    }
}
