<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Controller\Checkout;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PayYourWay\Pyw\Controller\Checkout\ReturnAction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReturnActionTest extends TestCase
{
    /** @var ObjectManager|MockObject */
    private $objectManager;

    /** @var ReturnAction */
    private $returnActionController;

    private function getForwardFactoryMockForCancel(): ForwardFactory
    {
        $forwardMock = $this->createMock(Forward::class);
        $forwardMock
            ->expects($this->once())
            ->method('forward')
            ->with('cancel')
            ->willReturnSelf();

        $forwardFactory = $this->getMockBuilder(ForwardFactory::class)
            ->setMethods([
                'create'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $forwardFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($forwardMock);

        return $forwardFactory;
    }

    private function getForwardFactoryMockForPlaceOrder(): ForwardFactory
    {
        $forwardMock = $this->createMock(Forward::class);
        $forwardMock
            ->expects($this->once())
            ->method('forward')
            ->with('placeOrder')
            ->willReturnSelf();

        $forwardFactory = $this->getMockBuilder(ForwardFactory::class)
            ->setMethods([
                'create'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $forwardFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($forwardMock);

        return $forwardFactory;
    }

    private function getMessageManagerMock(): MessageManagerInterface
    {
        return $this->createMock(MessageManagerInterface::class);
    }

    private function getRequestMockForCancel(): RequestInterface
    {
        $pywMsg = 'N';

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive(
                ['pywmsg'],
                ['pywmsg']
            )
            ->willReturn(
                $pywMsg,
                $pywMsg
            );

        return $request;
    }

    private function getRequestMockForPlaceOrder(): RequestInterface
    {
        $pywId = '12e28334-4dd4-4510-a40e-bf8d2c39ef55';
        $pywMsg = 'Y';

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive(
                ['pywmsg'],
                ['pywid'],
                ['pywmsg']
            )
            ->willReturn(
                $pywMsg,
                $pywId,
                $pywMsg
            );

        return $request;
    }

    private function getRequestMockForRedirect(): RequestInterface
    {
        $pywMsg = null;

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects($this->exactly(2))
            ->method('getParam')
            ->withConsecutive(
                ['pywmsg'],
                ['pywmsg']
            )
            ->willReturn(
                $pywMsg,
                $pywMsg
            );

        return $request;
    }

    private function getResultFactoryMock(): ResultFactory
    {
        $resultMock = $this->createMock(ResultInterface::class);

        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultMock);

        return $resultFactory;
    }

    private function getResultFactoryMockForRedirect(): ResultFactory
    {
        $path = 'checkout/cart';

        $resultMock = $this->getMockBuilder(ResultInterface::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $resultMock
            ->expects($this->once())
            ->method('setPath')
            ->with($path)
            ->willReturnSelf();

        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory
            ->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultMock);

        return $resultFactory;
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
    }

    private function getReturnActionControllerObject(
        MessageManagerInterface $messageManager,
        ResultFactory $resultFactory,
        RequestInterface $request,
        ForwardFactory $forwardFactory
    ): void {
        $this->returnActionController = $this->objectManager->getObject(
            ReturnAction::class,
            [
                'messageManager' => $messageManager,
                'resultFactory' => $resultFactory,
                'request' => $request,
                'forwardFactory' => $forwardFactory
            ]
        );
    }

    public function testForwardToPlaceOrder()
    {
        $messageManagerMock = $this->getMessageManagerMock();
        $resultFactoryMock = $this->getResultFactoryMock();
        $requestMockForPlaceOrder = $this->getRequestMockForPlaceOrder();
        $forwardFactoryMockForPlaceOrder = $this->getForwardFactoryMockForPlaceOrder();

        $this->getReturnActionControllerObject(
            $messageManagerMock,
            $resultFactoryMock,
            $requestMockForPlaceOrder,
            $forwardFactoryMockForPlaceOrder
        );

        $forwardMock = $this->createMock(Forward::class);

        $returnActionExecution = $this->returnActionController->execute();
        $this->assertInstanceOf(Forward::class, $returnActionExecution);
        $this->assertEquals($forwardMock, $returnActionExecution);
    }

    public function testForwardToCancel()
    {
        $messageManagerMock = $this->getMessageManagerMock();
        $resultFactoryMock = $this->getResultFactoryMock();
        $requestMockForCancel = $this->getRequestMockForCancel();
        $forwardFactoryMockForCancel = $this->getForwardFactoryMockForCancel();

        $this->getReturnActionControllerObject(
            $messageManagerMock,
            $resultFactoryMock,
            $requestMockForCancel,
            $forwardFactoryMockForCancel
        );

        $forwardMock = $this->createMock(Forward::class);

        $returnActionExecution = $this->returnActionController->execute();
        $this->assertInstanceOf(Forward::class, $returnActionExecution);
        $this->assertEquals($forwardMock, $returnActionExecution);
    }

    public function testRedirectWhenMissingParameters()
    {
        $messageManagerMock = $this->getMessageManagerMock();
        $resultFactoryMock = $this->getResultFactoryMockForRedirect();
        $requestMockForRedirect = $this->getRequestMockForRedirect();
        $forwardFactoryMock =  $this->createMock(ForwardFactory::class);

        $this->getReturnActionControllerObject(
            $messageManagerMock,
            $resultFactoryMock,
            $requestMockForRedirect,
            $forwardFactoryMock
        );

        $this->assertInstanceOf(ResultInterface::class, $this->returnActionController->execute());
    }
}
