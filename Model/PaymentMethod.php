<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Api\PaymentReturnLookupInterface;
use PayYourWay\Pyw\Api\PaymentReturnRequestInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Api\RequestInterface as PaymentConfirmationRequestInterface;
use PayYourWay\Pyw\Model\Adminhtml\Source\Environment;

class PaymentMethod extends AbstractMethod
{
    public const METHOD_CODE = 'payyourway';

    /** @var string */
    protected $_code = self::METHOD_CODE;
    protected $_canCapture = true;
    protected $_canRefund = true;

    private OrderRepositoryInterface $orderRepository;
    private PaymentConfirmationRequestInterface $paymentConfirmationRequest;
    private PaymentReturnRequestInterface $paymentReturnRequest;
    private RefIdBuilderInterface $refIdBuilder;
    private ConfigInterface $config;
    private GenerateAccessTokenInterface $generateAccessToken;
    private PaymentReturnLookupInterface $paymentReturnLookup;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null,
        OrderRepositoryInterface $orderRepository,
        PaymentReturnRequestInterface $paymentReturnRequest,
        RefIdBuilderInterface $refIdBuilder,
        ConfigInterface $config,
        GenerateAccessTokenInterface $generateAccessToken,
        PaymentReturnLookupInterface $paymentReturnLookup,
        PaymentConfirmationRequestInterface $paymentConfirmationRequest
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

        $this->orderRepository = $orderRepository;
        $this->paymentReturnRequest = $paymentReturnRequest;
        $this->refIdBuilder = $refIdBuilder;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        $this->paymentReturnLookup = $paymentReturnLookup;
        $this->paymentConfirmationRequest = $paymentConfirmationRequest;
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
        $orderId = '41';
        $order = $this->orderRepository->get($orderId);

        $this->paymentReturnRequest->setContentType('application/json');
        $this->paymentReturnRequest->setAccept('application/json');
        $this->paymentReturnRequest->setChannel('PYW_ONLINE');
        $this->paymentReturnRequest->setClientId('PYW');
        $this->paymentReturnRequest->setTransactionId($orderId);
        $this->paymentReturnRequest->setRefId($this->refIdBuilder->buildRefId(
            $this->config->getClientId(),
            $this->generateAccessToken->execute(),
            $this->config->getClientId(),
            time(),
            $orderId,
            $order->getCustomerEmail() ?? ''
        ));

        $this->paymentReturnRequest->setAuthCode($this->getAuthCode($order));
        $this->paymentReturnRequest->setReturnAmount((string)$order->getTotalRefunded());
        $this->paymentReturnRequest->setReturnPayments([]);

        $paymentReturnResponse = json_decode($this->paymentReturnLookup->lookup($this->paymentReturnRequest));

        if (!is_object($paymentReturnResponse)) {
            $this->logger->error(
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
     * @param OrderInterface $order
     * @return string
     * @throws LocalizedException
     */
    private function getAuthCode(OrderInterface $order): string
    {
        $merchantId = $this->config->getClientId();

        if ($merchantId === null) {
            throw new LocalizedException(
                __('There is an issue with the merchant ID.')
            );
        }

        /**
         * Make a request to Payment Confirmation API in order to check the payment details
         */
        $accessToken =  $this->generateAccessToken->execute();
        $sandboxMode = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX;
        $this->paymentConfirmationRequest->setChannel('ONLINE');
        $this->paymentConfirmationRequest->setMerchantId($merchantId);
        $this->paymentConfirmationRequest->setPywid((string)$order->getPayment()->getTransactionId());
        $this->paymentConfirmationRequest->setTransactionId($order->getId());
        $this->paymentConfirmationRequest->setActionType('READONLY');
        $this->paymentConfirmationRequest->setTransactionType('1P');
        $refId = $this->refIdBuilder->buildRefId(
            $merchantId,
            $accessToken,
            $merchantId,
            time(),
            $order->getId(),
            $order->getCustomerEmail() ?? '',
            $sandboxMode
        );
        $this->paymentConfirmationRequest->setRefId($refId);

        $paymentConfirmationResponse = json_decode($this->paymentConfirmationLookup->lookup(
            $this->paymentConfirmationRequest
        ));

        $debug = [
            'client_id'=>$this->config->getClientId(),
            'access_token'=>$accessToken,
            'requestor_id'=>$this->config->getClientId(),
            'timestamp'=>time(),
            'transaction_id'=>$order->getId(),
            'user_id'=>$order->getCustomerEmail() ?? '',
            'sandbox_mode'=>$sandboxMode,
            'ref_id'=>$refId
        ];
        $this->logger->debug(json_encode($debug));

        if (!is_object($paymentConfirmationResponse)) {
            throw new LocalizedException(
                __('There is an issue with the payment gateway provider.')
            );
        }

        $this->logger->debug(json_encode($paymentConfirmationResponse));

        return $paymentConfirmationResponse->authCode;
    }
}
