<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Controller\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Psr\Log\LoggerInterface;

class Cancel implements HttpGetActionInterface
{
    private MessageManagerInterface $messageManager;
    private ResultFactory $resultFactory;
    private LoggerInterface $logger;
    private CheckoutSession $checkoutSession;

    public function __construct(
        MessageManagerInterface $messageManager,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CheckoutSession $checkoutSession
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(): ResultInterface
    {
        $this->messageManager->addSuccessMessage(
            __('Pay Your Way Checkout has been canceled.')
        );

        $quote = [];
        try {
            $quote = $this->checkoutSession->getQuote();
        } catch (NoSuchEntityException | LocalizedException $exception) {
            $this->logger->error(
                'Something went wrong with the current Quote.',
                [
                    'quote' => $quote,
                    'exception' => (string)$exception,
                ]
            );
        }

        $this->logger->error(
            'The Pay Your Way Checkout has been canceled.',
            [
                'quote' => $quote->getData()
            ]
        );

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('checkout/cart');
    }
}
