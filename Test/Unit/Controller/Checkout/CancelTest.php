<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Controller\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote;
use PayYourWay\Pyw\Controller\Checkout\Cancel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CancelTest extends TestCase
{
    /** @var ObjectManager|MockObject */
    private $objectManager;

    /** @var Cancel */
    private $cancelController;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
    }

    public function getCheckoutSessionMock()
    {
        $quoteMock = $this->createMock(Quote::class);

        $checkoutSession = $this->createMock(CheckoutSession::class);
        $checkoutSession
            ->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        return $checkoutSession;
    }

    public function getLoggerMock()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->willReturn(null);

        return $logger;
    }

    private function getMessageManagerMock(): MessageManagerInterface
    {
        $successMessage = 'Pay Your Way Checkout has been canceled.';

        $messageManager = $this->createMock(MessageManagerInterface::class);
        $messageManager
            ->expects($this->once())
            ->method('addSuccessMessage')
            ->with($successMessage)
            ->willReturnSelf();

        return $messageManager;
    }

    private function getResultFactoryMock(): ResultFactory
    {
        $redirect = 'checkout/cart';

        $resultMock = $this->getMockBuilder(ResultInterface::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $resultMock
            ->expects($this->once())
            ->method('setPath')
            ->with($redirect)
            ->willReturnSelf();

        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultMock);

        return $resultFactory;
    }

    private function getCancelControllerObject(
        MessageManagerInterface $messageManager,
        ResultFactory $resultFactory,
        LoggerInterface $logger,
        CheckoutSession $checkoutSession
    ): void {
        $this->cancelController = $this->objectManager->getObject(
            Cancel::class,
            [
                'messageManager' => $messageManager,
                'resultFactory' => $resultFactory,
                'logger' => $logger,
                'checkoutSession' => $checkoutSession
            ]
        );
    }

    public function testRedirectToCart()
    {
        $messageManagerMock = $this->getMessageManagerMock();
        $resultFactoryMock = $this->getResultFactoryMock();
        $loggerMock = $this->getLoggerMock();
        $checkoutSessionMock = $this->getCheckoutSessionMock();

        $this->getCancelControllerObject(
            $messageManagerMock,
            $resultFactoryMock,
            $loggerMock,
            $checkoutSessionMock
        );

        $cancelExecution = $this->cancelController->execute();

        $this->assertInstanceOf(ResultInterface::class, $cancelExecution);
    }
}
