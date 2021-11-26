<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Test\Unit\Model;

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
        return $this->createMock(Collection::class);
    }

    private function getConfigMock(): Config
    {
        return $this->createMock(Config::class);
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

    public function testGenerateAccessToken()
    {
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
        $this->assertTrue($generateAccessTokenExecution);
    }
}
