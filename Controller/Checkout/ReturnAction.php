<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Controller\Checkout;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class PlaceOrder implements HttpGetActionInterface
{
    private MessageManagerInterface $messageManager;
    private ResultFactory $resultFactory;
    private RequestInterface $request;

    public function __construct(
        MessageManagerInterface $messageManager,
        ResultFactory $resultFactory,
        RequestInterface $request
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
    }

    /**
     * Return from Pay Your Way when success
     *
     * @return void|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($this->request->getParam('pywmsg') === 'Y' && $this->request->getParam('pywid') !== null) {
            $this->request->initForward();
            $this->request->setActionName('placeOrder');
            $this->request->setDispatched(false);
            return;
        }

        $this->messageManager->addErrorMessage(
            __('Something went wrong during the Pay Your Way Checkout. Please try again later.')
        );

        return $resultRedirect->setPath('checkout/cart');
    }
}
