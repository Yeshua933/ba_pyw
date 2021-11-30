<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;
use PayYourWay\Pyw\Cron\RefreshToken;
use PayYourWay\Pyw\Model\Config;
use PayYourWay\Pyw\Model\GenerateAccessToken;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RefreshTokenTest extends TestCase
{
    /** @var ObjectManager|MockObject */
    private $objectManager;

    /** @var RefreshToken */
    private $refreshTokenCron;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
    }

    private function getConfigMockForPayYourWayAndRefreshTokenProcessEnabled(): Config
    {
        $config = $this->createMock(Config::class);
        $config
            ->expects($this->once())
            ->method('isPayYourWayEnabled')
            ->willReturn(true);

        $config
            ->expects($this->once())
            ->method('isRefreshTokenProcessEnabled')
            ->willReturn(true);

        return $config;
    }

    private function getConfigMockForRefreshTokenProcessDisabled(): Config
    {
        $config = $this->createMock(Config::class);
        $config
            ->expects($this->once())
            ->method('isPayYourWayEnabled')
            ->willReturn(true);

        $config
            ->expects($this->once())
            ->method('isRefreshTokenProcessEnabled')
            ->willReturn(false);

        return $config;
    }

    private function getConfigMockForPayYourWayDisabled(): Config
    {
        $config = $this->createMock(Config::class);
        $config
            ->expects($this->once())
            ->method('isPayYourWayEnabled')
            ->willReturn(false);

        $config
            ->expects($this->never())
            ->method('isRefreshTokenProcessEnabled');

        return $config;
    }

    private function getGenerateAccessTokenMockForPayYourWayAndRefreshTokenProcessEnabled()
    {
        $accessToken = 'ACCESS_TOKEN_RANDOM_STRING';

        $generateAccessToken = $this->createMock(GenerateAccessToken::class);
        $generateAccessToken
            ->expects($this->once())
            ->method('execute')
            ->willReturn($accessToken);

        return $generateAccessToken;
    }

    private function getGenerateAccessTokenMockForPayYourWayDisabled()
    {
        $generateAccessToken = $this->createMock(GenerateAccessToken::class);
        $generateAccessToken
            ->expects($this->never())
            ->method('execute');

        return $generateAccessToken;
    }

    private function getRefreshTokenCronObject(
        Config $config,
        GenerateAccessTokenInterface $generateAccessToken
    ): void {
        $this->refreshTokenCron = $this->objectManager->getObject(
            RefreshToken::class,
            [
                'config' => $config,
                'generateAccessToken' => $generateAccessToken
            ]
        );
    }

    public function testExecuteCronWhenPayYourWayAndRefreshTokenProcessEnabled()
    {
        $configMock = $this->getConfigMockForPayYourWayAndRefreshTokenProcessEnabled();
        $generateAccessTokenMock = $this->getGenerateAccessTokenMockForPayYourWayAndRefreshTokenProcessEnabled();

        $this->getRefreshTokenCronObject(
            $configMock,
            $generateAccessTokenMock
        );

        $this->refreshTokenCron->execute();
    }

    public function testSkipCronWhenPayYourWayDisabled()
    {
        $configMock = $this->getConfigMockForPayYourWayDisabled();
        $generateAccessTokenMock = $this->getGenerateAccessTokenMockForPayYourWayDisabled();

        $this->getRefreshTokenCronObject(
            $configMock,
            $generateAccessTokenMock
        );

        $this->refreshTokenCron->execute();
    }

    public function testSkipCronWhenRefreshTokenProcessDisabled()
    {
        $configMock = $this->getConfigMockForRefreshTokenProcessDisabled();
        $generateAccessTokenMock = $this->getGenerateAccessTokenMockForPayYourWayDisabled();

        $this->getRefreshTokenCronObject(
            $configMock,
            $generateAccessTokenMock
        );

        $this->refreshTokenCron->execute();
    }
}
