<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PayYourWay\Pyw\Api\AccessTokenLookupInterface;
use PayYourWay\Pyw\Api\AccessTokenRequestInterface;
use PayYourWay\Pyw\Api\AccessTokenRequestInterfaceFactory;
use PayYourWay\Pyw\Model\AccessToken;
use PayYourWay\Pyw\Model\AccessTokenFactory;
use PayYourWay\Pyw\Model\Config;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken as ResourceModel;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken\Collection;
use phpseclib\Crypt\RSA;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GenerateAccessTokenTest extends TestCase
{
    /** @var ObjectManager|MockObject */
    private $objectManager;

    /** @var GenerateAccessToken */
    private $generateAccessTokenModel;

    private function getAccessTokenLookupMockForDeletingAndGeneratingAccessToken(): AccessTokenLookupInterface
    {
        $accessTokenLookup = $this->createMock(AccessTokenLookupInterface::class);
        $accessTokenLookup
            ->expects($this->once())
            ->method('execute');

        return $accessTokenLookup;
    }

    private function getAccessTokenLookupMockForGettingAccessToken(): AccessTokenLookupInterface
    {
        return $this->createMock(AccessTokenLookupInterface::class);
    }

    private function getAccessTokenFactoryMockForDeletingAndGeneratingAccessToken(): AccessTokenFactory
    {
        $accessToken = "ACCESS_TOKEN_RANDOM_STRING";

        $accessTokenMock = $this->getMockBuilder(AccessToken::class)
            ->setMethods([
                'setAccessToken',
                'setTokenType',
                'setExp',
                'setIss',
                'getAccessToken'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $accessTokenMock
            ->expects($this->once())
            ->method('setAccessToken')
            ->willReturnSelf();

        $accessTokenMock
            ->expects($this->once())
            ->method('setTokenType')
            ->willReturnSelf();

        $accessTokenMock
            ->expects($this->once())
            ->method('setExp')
            ->willReturnSelf();

        $accessTokenMock
            ->expects($this->once())
            ->method('setIss')
            ->willReturnSelf();

        $accessTokenMock
            ->expects($this->once())
            ->method('getAccessToken')
            ->willReturn($accessToken);

        $accessTokenFactory = $this->createMock(AccessTokenFactory::class);
        $accessTokenFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($accessTokenMock);

        return $accessTokenFactory;
    }

    private function getAccessTokenFactoryMockForGettingAccessToken(): AccessTokenFactory
    {
        return $this->createMock(AccessTokenFactory::class);
    }

    private function getAccessTokenRequestFactoryMockForDeletingAndGeneratingAccessToken():
    AccessTokenRequestInterfaceFactory
    {
        $granType = 'urn:ietf:params:oauth:grant-type:jwt-bearer';
        $contentType = 'application/json';

        $accessTokenRequestMock = $this->createMock(AccessTokenRequestInterface::class);
        $accessTokenRequestMock
            ->expects($this->once())
            ->method('setJwt')
            ->willReturnSelf();

        $accessTokenRequestMock
            ->expects($this->once())
            ->method('setGrantType')
            ->with($granType)
            ->willReturnSelf();

        $accessTokenRequestMock
            ->expects($this->once())
            ->method('setContentType')
            ->with($contentType)
            ->willReturnSelf();

        $accessTokenRequestFactory = $this->createMock(AccessTokenRequestInterfaceFactory::class);
        $accessTokenRequestFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($accessTokenRequestMock);

        return $accessTokenRequestFactory;
    }

    private function getAccessTokenRequestFactoryMockForGettingAccessToken(): AccessTokenRequestInterfaceFactory
    {
        return $this->createMock(AccessTokenRequestInterfaceFactory::class);
    }

    private function getCollectionMockForDeletingAndGeneratingAccessToken(): Collection
    {
        $accessTokenId = 10;
        $expirationTime = strtotime(date("Y-m-d H:i:s", strtotime("+14 minutes"))) * 1000;

        $abstractModelMock = $this->getMockBuilder(AbstractModel::class)
            ->setMethods([
                'getId',
                'getExp'
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $abstractModelMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($accessTokenId);

        $abstractModelMock
            ->expects($this->once())
            ->method('getExp')
            ->willReturn($expirationTime);

        $collection = $this->createMock(Collection::class);
        $collection
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($abstractModelMock);

        return $collection;
    }

    private function getCollectionMockForGettingAccessToken(): Collection
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

    private function getConfigMockForDeletingAndGeneratingAccessToken(): Config
    {
        $clientId = 'PYW';
        $rsa = new RSA();
        $keyPair = $rsa->createKey();
        $privateKey = $keyPair['privatekey'];
        $environment = 'sandbox';

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
            ->expects($this->exactly(2))
            ->method('isDebugMode')
            ->willReturn(false);

        $config
            ->expects($this->once())
            ->method('getEnvironment')
            ->willReturn($environment);

        return $config;
    }

    private function getConfigMockForGettingAccessToken(): Config
    {
        $clientId = 'PYW';
        $rsa = new RSA();
        $keyPair = $rsa->createKey();
        $privateKey = $keyPair['privatekey'];

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

    private function getResourceModelMockForGettingAccessToken(): ResourceModel
    {
        return $this->createMock(ResourceModel::class);
    }

    private function getResourceModelMockForDeletingAndGeneratingAccessToken(): ResourceModel
    {
        $resourceModel = $this->createMock(ResourceModel::class);
        $resourceModel
            ->expects($this->once())
            ->method('delete')
            ->willReturnSelf();

        $resourceModel
            ->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        return $resourceModel;
    }

    private function getSerializerMockForDeletingAndGeneratingAccessToken(): SerializerInterface
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer
            ->expects($this->once())
            ->method('unserialize')
            ->willReturn([]);

        return $serializer;
    }

    private function getSerializerMockForGettingAccessToken(): SerializerInterface
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

        $configMock = $this->getConfigMockForGettingAccessToken();
        $accessTokenRequestFactoryMock = $this->getAccessTokenRequestFactoryMockForGettingAccessToken();
        $accessTokenLookupMock = $this->getAccessTokenLookupMockForGettingAccessToken();
        $accessTokenFactoryMock = $this->getAccessTokenFactoryMockForGettingAccessToken();
        $resourceModelMock = $this->getResourceModelMockForGettingAccessToken();
        $serializerMock = $this->getSerializerMockForGettingAccessToken();
        $collectionMock = $this->getCollectionMockForGettingAccessToken();
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

    public function testDeleteAndGenerateAccessToken()
    {
        $accessToken = "ACCESS_TOKEN_RANDOM_STRING";

        $configMock = $this->getConfigMockForDeletingAndGeneratingAccessToken();
        $accessTokenRequestFactoryMock = $this->getAccessTokenRequestFactoryMockForDeletingAndGeneratingAccessToken();
        $accessTokenLookupMock = $this->getAccessTokenLookupMockForDeletingAndGeneratingAccessToken();
        $accessTokenFactoryMock = $this->getAccessTokenFactoryMockForDeletingAndGeneratingAccessToken();
        $resourceModelMock = $this->getResourceModelMockForDeletingAndGeneratingAccessToken();
        $serializerMock = $this->getSerializerMockForDeletingAndGeneratingAccessToken();
        $collectionMock = $this->getCollectionMockForDeletingAndGeneratingAccessToken();
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
