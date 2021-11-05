<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Controller\Checkout;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class Cancel implements HttpGetActionInterface
{
    private MessageManagerInterface $messageManager;
    private ResultFactory $resultFactory;

    public function __construct(
        MessageManagerInterface $messageManager,
        ResultFactory $resultFactory
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
    }

    public function execute(): ResultInterface
    {
        $this->messageManager->addSuccessMessage(
            __('Pay Your Way Checkout has been canceled.')
        );

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('checkout/cart');
    }
}
