<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PayYourWay\Pyw\Api\AccessTokenLookupInterface;
use PayYourWay\Pyw\Api\AccessTokenRequestInterfaceFactory;
use PayYourWay\Pyw\Model\AccessTokenFactory;
use PayYourWay\Pyw\Model\Config;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken as ResourceModel;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GenerateAccessTokenTest extends TestCase
{
    /** @var ObjectManager|MockObject */
    private $objectManager;

    /** @var GenerateAccessToken */
    private $generateAccessTokenModel;

    private function getAccessTokenLookupMock(): AccessTokenLookupInterface
    {
        return $this->createMock(AccessTokenLookupInterface::class);
    }

    private function getAccessTokenFactoryMock(): AccessTokenFactory
    {
        return $this->createMock(AccessTokenFactory::class);
    }

    private function getAccessTokenRequestFactoryMock(): AccessTokenRequestInterfaceFactory
    {
        return $this->createMock(AccessTokenRequestInterfaceFactory::class);
    }

    private function getCollectionMock(): Collection
    {
        $accessTokenId = 10;
        $expirationTime = strtotime(date("Y-m-d H:i:s", strtotime("+16 minutes"))) * 1000;
        $accessToken = "ACCESS_TOKEN_RANDOM_STRING";

        $dataObjectMock = $this->getMockBuilder(DataObject::class)
            ->setMethods([
                'getId',
                'getExp',
                'getAccessToken'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $dataObjectMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($accessTokenId);

        $dataObjectMock
            ->expects($this->once())
            ->method('getExp')
            ->willReturn($expirationTime);

        $dataObjectMock
            ->expects($this->once())
            ->method('getAccessToken')
            ->willReturn($accessToken);

        $collection = $this->createMock(Collection::class);
        $collection
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($dataObjectMock);

        return $collection;
    }

    private function getConfigMock(): Config
    {
        $clientId = 'PYW';
        $privateKey = 'PRIVATE_KEY';

        $config = $this->createMock(Config::class);
        $config
            ->expects($this->once())
            ->method('getClientId')
            ->willReturn($clientId);

        $config
            ->expects($this->once())
            ->method('getPrivateKey')
            ->willReturn($privateKey);

        $config
            ->expects($this->once())
            ->method('isDebugMode')
            ->willReturn(false);

        return $config;
    }

    private function getLoggerMock(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }

    private function getResourceModelMock(): ResourceModel
    {
        return $this->createMock(ResourceModel::class);
    }

    private function getSerializerMock(): SerializerInterface
    {
        return $this->createMock(SerializerInterface::class);
    }

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
    }

    private function getGenerateAccessTokenModelObject(
        Config $config,
        AccessTokenRequestInterfaceFactory $accessTokenRequestFactory,
        AccessTokenLookupInterface $accessTokenLookup,
        AccessTokenFactory $accessTokenFactory,
        ResourceModel $resourceModel,
        SerializerInterface $serializer,
        Collection $collection,
        LoggerInterface $logger
    ): void {
        $this->generateAccessTokenModel = $this->objectManager->getObject(
            GenerateAccessToken::class,
            [
                'config' => $config,
                'accessTokenRequestFactory' => $accessTokenRequestFactory,
                'accessTokenLookup' => $accessTokenLookup,
                'resourceModel' => $resourceModel,
                'accessTokenFactory' => $accessTokenFactory,
                'serializer' => $serializer,
                'collection' => $collection,
                'logger' => $logger
            ]
        );
    }

    public function testGetAccessToken()
    {
        $accessToken = "ACCESS_TOKEN_RANDOM_STRING";

        $configMock = $this->getConfigMock();
        $accessTokenRequestFactoryMock = $this->getAccessTokenRequestFactoryMock();
        $accessTokenLookupMock = $this->getAccessTokenLookupMock();
        $accessTokenFactoryMock = $this->getAccessTokenFactoryMock();
        $resourceModelMock = $this->getResourceModelMock();
        $serializerMock = $this->getSerializerMock();
        $collectionMock = $this->getCollectionMock();
        $loggerMock = $this->getLoggerMock();

        $this->getGenerateAccessTokenModelObject(
            $configMock,
            $accessTokenRequestFactoryMock,
            $accessTokenLookupMock,
            $accessTokenFactoryMock,
            $resourceModelMock,
            $serializerMock,
            $collectionMock,
            $loggerMock
        );

        $generateAccessTokenExecution = $this->generateAccessTokenModel->execute();
        $this->assertEquals($accessToken, $generateAccessTokenExecution);
    }
}
