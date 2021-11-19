<?php

namespace PayYourWay\Pyw\Plugin;

use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\Data\CreditmemoCommentCreationInterface;
use Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\RefundOrderInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Api\PaymentReturnLookupInterface;
use PayYourWay\Pyw\Api\PaymentReturnRequestInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;
use Psr\Log\LoggerInterface;

class RefundOrderPlugin
{
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
        OrderRepositoryInterface $orderRepository
    ) {
        $this->paymentReturnLookup = $paymentReturnLookup;
        $this->paymentReturnRequest = $paymentReturnRequest;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->refIdBuilder = $refIdBuilder;
        $this->config = $config;
        $this->generateAccessToken = $generateAccessToken;
        $this->orderRepository = $orderRepository;
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
        $this->paymentReturnRequest->setClientId('CLIENT ID');
        $this->paymentReturnRequest->setTransactionId('TRANSACTION ID');
        $this->paymentReturnRequest->setRefId($this->refIdBuilder->buildRefId(
            $this->config->getClientId(),
            $this->generateAccessToken->execute(),
            $this->config->getClientId(),
            time(),
            $result,
            $order->getCustomerEmail() ?? ''
        ));

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
}
