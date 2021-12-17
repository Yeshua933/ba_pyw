<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Api\Data\OrderInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\PaymentReturnLookupInterface;
use PayYourWay\Pyw\Api\PaymentReturnRequestInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Api\RequestInterface as PaymentConfirmationRequestInterface;
use Psr\Log\LoggerInterface;

class PaymentMethod extends AbstractMethod
{
    public const METHOD_CODE = 'payyourway';

    /** @var string */
    protected $_code = self::METHOD_CODE;
    protected $_canCapture = true;
    protected $_canRefund = true;

    private PaymentConfirmationRequestInterface $paymentConfirmationRequest;
    private PaymentReturnRequestInterface $paymentReturnRequest;
    private RefIdBuilderInterface $refIdBuilder;
    private ConfigInterface $config;
    private GenerateAccessTokenInterface $generateAccessToken;
    private PaymentReturnLookupInterface $paymentReturnLookup;
    private PaymentConfirmationLookupInterface $paymentConfirmationLookup;
    private LoggerInterface $pywLogger;

    public function __construct(
        Context                             $context,
        Registry                            $registry,
        ExtensionAttributesFactory          $extensionFactory,
        AttributeValueFactory               $customAttributeFactory,
        Data                                $paymentData,
        ScopeConfigInterface                $scopeConfig,
        Logger                              $logger,
        PaymentReturnRequestInterface       $paymentReturnRequest,
        RefIdBuilderInterface               $refIdBuilder,
        ConfigInterface                     $config,
        GenerateAccessTokenInterface        $generateAccessToken,
        PaymentReturnLookupInterface        $paymentReturnLookup,
        PaymentConfirmationRequestInterface $paymentConfirmationRequest,
        PaymentConfirmationLookupInterface  $paymentConfirmationLookup,
        LoggerInterface                     $pywLogger,
        AbstractResource                    $resource = null,
        AbstractDb                          $resourceCollection = null,
        array                               $data = [],
        DirectoryHelper                     $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );

        $this->paymentReturnRequest = $paymentReturnRequest;
        $this->refIdBuilder = $refIdBuilder;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        $this->paymentReturnLookup = $paymentReturnLookup;
        $this->paymentConfirmationRequest = $paymentConfirmationRequest;
        $this->paymentConfirmationLookup = $paymentConfirmationLookup;
        $this->pywLogger = $pywLogger;
    }

    public function assignData(DataObject $data): PaymentMethod
    {
        if (is_array($data->getAdditionalData())) {
            foreach ($data->getAdditionalData() as $key => $value) {
                $this->getInfoInstance()->setAdditionalInformation($key, $value);
            }
        }
        return parent::assignData($data);
    }

    public function capture(InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);
        $additionalInformation = $payment->getAdditionalInformation();
        $payment->setTransactionId($additionalInformation['pywid']);
        $payment->setLastTransId($additionalInformation['pywid']);
        return $this;
    }

    /**
     * @return bool
     */
    public function canRefund(): bool
    {
        return $this->_canRefund;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this|PaymentMethod
     * @throws LocalizedException
     */
    public function refund(InfoInterface $payment, $amount): PaymentMethod
    {
        $order = $payment->getOrder();

        $this->paymentReturnRequest->setContentType('application/json');
        $this->paymentReturnRequest->setAccept('application/json');
        $this->paymentReturnRequest->setChannel('ONLINE');
        $this->paymentReturnRequest->setClientId((string)$this->config->getClientId());
        $this->paymentReturnRequest->setTransactionId((string)$order->getQuoteId());
        $this->paymentReturnRequest->setRefId($this->refIdBuilder->buildRefId(
            $this->config->getClientId(),
            $this->generateAccessToken->execute(),
            $this->config->getClientId(),
            time(),
            $order->getQuoteId(),
            $order->getCustomerEmail() ?? ''
        ));

        $this->paymentReturnRequest->setAuthCode($this->getAuthCode($payment));
        $this->paymentReturnRequest->setReturnAmount((string)$amount);
        $this->paymentReturnRequest->setReturnPayments([]);

        $paymentReturnResponse = $this->paymentReturnLookup->lookup($this->paymentReturnRequest);
        $paymentReturnResponseDecoded = json_decode($this->paymentReturnLookup->lookup($this->paymentReturnRequest));

        if ($this->config->isDebugMode()) {
            $this->pywLogger->info(json_encode($this->paymentReturnRequest->getBody()));
            $this->pywLogger->info(json_encode($this->paymentReturnRequest->getHeaders()));
            $this->pywLogger->info($paymentReturnResponse);
        }

        if (!is_object($paymentReturnResponseDecoded)) {
            $this->pywLogger->error(
                'There is an issue with the payment return API',
                [
                    'transactionId' => $this->paymentReturnRequest->getTransactionId(),
                ]
            );
        }

        return $this;
    }

    /**
     * Returns the Auth code to be used in the refund request
     *
     * @param InfoInterface $payment
     * @return string
     */
    private function getAuthCode(InfoInterface $payment): string
    {
        $additionalInformation = $payment->getAdditionalInformation();
        return $additionalInformation['auth_code'];
    }

    /**
     * Check whether payment method can be used
     *
     * @return bool
     */
    public function isPYWAvailable(): bool
    {
        $accessToken = $this->generateAccessToken->execute();
        if (!isset($accessToken)) {
            return false;
        }
        return true;
    }
}
