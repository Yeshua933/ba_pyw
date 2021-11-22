<?php

namespace PayYourWay\Pyw\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\Data\CreditmemoCommentCreationInterface;
use Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\RefundOrderInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Api\PaymentReturnLookupInterface;
use PayYourWay\Pyw\Api\PaymentReturnRequestInterface;
use PayYourWay\Pyw\Api\PaymentConfirmationLookupInterface;
use PayYourWay\Pyw\Api\RequestInterface as PaymentConfirmationRequestInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use PayYourWay\Pyw\Model\Adminhtml\Source\Environment;
use Psr\Log\LoggerInterface;

class RefundOrderPlugin
{
    private PaymentConfirmationLookupInterface $paymentConfirmationLookup;
    private PaymentConfirmationRequestInterface $paymentConfirmationRequest;
    private PaymentReturnLookupInterface $paymentReturnLookup;
    private PaymentReturnRequestInterface $paymentReturnRequest;
    private LoggerInterface $logger;
    private ManagerInterface $messageManager;
    private RefIdBuilderInterface $refIdBuilder;
    private ConfigInterface $config;
    private GenerateAccessTokenInterface $generateAccessToken;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        PaymentReturnLookupInterface $paymentReturnLookup,
        PaymentReturnRequestInterface $paymentReturnRequest,
        LoggerInterface $logger,
        ManagerInterface $messageManager,
        RefIdBuilderInterface $refIdBuilder,
        ConfigInterface $config,
        GenerateAccessTokenInterface $generateAccessToken,
        OrderRepositoryInterface $orderRepository,
        PaymentConfirmationRequestInterface $paymentConfirmationRequest,
        PaymentConfirmationLookupInterface $paymentConfirmationLookup
    ) {
        $this->paymentReturnLookup = $paymentReturnLookup;
        $this->paymentReturnRequest = $paymentReturnRequest;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->refIdBuilder = $refIdBuilder;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        $this->orderRepository = $orderRepository;
        $this->paymentConfirmationRequest = $paymentConfirmationRequest;
        $this->paymentConfirmationLookup = $paymentConfirmationLookup;
    }

    /**
     * @param RefundOrderInterface $subject
     * @param int $result
     * @param int $orderId
     * @param CreditmemoItemCreationInterface[] $items
     * @param bool|null $notify
     * @param bool|null $appendComment
     * @param CreditmemoCommentCreationInterface|null $comment
     * @param CreditmemoCreationArgumentsInterface|null $arguments
     * @return int
     * @throws LocalizedException
     */
    public function afterExecute(
        RefundOrderInterface $subject,
        int $result,
        $orderId,
        array $items = [],
        $notify = false,
        $appendComment = false,
        CreditmemoCommentCreationInterface $comment = null,
        CreditmemoCreationArgumentsInterface $arguments = null
    ): int {
        $order = $this->orderRepository->get($result);

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
            $result,
            $order->getCustomerEmail() ?? ''
        ));

        $this->paymentReturnRequest->setAuthCode($this->getAuthCode($order));
        $this->paymentReturnRequest->setReturnAmount($order->getTotalRefunded());
        $this->paymentReturnRequest->setReturnPayments([]);

        $paymentReturnResponse = json_decode($this->paymentReturnLookup->lookup($this->paymentReturnRequest));

        if (!is_object($paymentReturnResponse)) {
            $this->logger->error(
                'There is an issue with the payment return API',
                [
                    'transactionId' => $this->paymentReturnRequest->getTransactionId(),
                ]
            );
            $this->messageManager->addErrorMessage(
                __('There is an issue with the payment return API')
            );
        }

        return $result;
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
        $this->paymentConfirmationRequest->setPywid($order->getPayment()->getTransactionId());
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
