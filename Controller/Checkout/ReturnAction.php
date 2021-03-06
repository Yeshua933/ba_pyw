<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Controller\Checkout;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class ReturnAction implements HttpGetActionInterface
{
    private MessageManagerInterface $messageManager;
    private ResultFactory $resultFactory;
    private RequestInterface $request;
    private ForwardFactory $forwardFactory;

    public function __construct(
        MessageManagerInterface $messageManager,
        ResultFactory $resultFactory,
        RequestInterface $request,
        ForwardFactory $forwardFactory
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->forwardFactory = $forwardFactory;
    }

    /**
     * Handle the return from Pay Your Way
     *
     * @return void|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        /**
         * Proceed to the place order action if the parameters indicates the order has been successful
         */
        if ($this->request->getParam('pywmsg') === 'Y' && $this->request->getParam('pywid') !== null) {
            $resultForward = $this->forwardFactory->create();
            $resultForward->forward('placeOrder');
            return $resultForward;
        }

        /**
         * Proceed to the cancel order action if the parameters indicates the order hasn't been successful
         */
        if ($this->request->getParam('pywmsg') === 'N') {
            $resultForward = $this->forwardFactory->create();
            $resultForward->forward('cancel');
            return $resultForward;
        }

        $this->messageManager->addErrorMessage(
            __('Something went wrong during the Pay Your Way Checkout. Please try again later.')
        );

        return $resultRedirect->setPath('checkout/cart');
    }
}
