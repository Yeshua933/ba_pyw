<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service;

use Magento\Framework\HTTP\ClientInterface;
use PayYourWay\Pyw\Api\AccessTokenLookupInterface;
use PayYourWay\Pyw\Api\AccessTokenRequestInterface;
use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Model\Adminhtml\Source\Environment;

class AccessTokenLookup implements AccessTokenLookupInterface
{
    private ClientInterface $curl;
    private ConfigInterface $config;
    private const OAUTH_UAT = 'https://oauth.uat.telluride.transformco.com/oauthAS/service/oAuth/token.json';
    private const OAUTH_PRD = 'https://oauth.telluride.transformco.com/oauthAS/service/oAuth/token.json';

    public function __construct(
        ClientInterface $curl,
        ConfigInterface $config
    ) {
        $this->curl = $curl;
        $this->config = $config;
    }

    public function execute(
        AccessTokenRequestInterface $request
    ): string {
        $url = $this->config->getEnvironment() === Environment::ENVIRONMENT_SANDBOX ?
            self::OAUTH_UAT :
            self::OAUTH_PRD;

        $this->curl->setHeaders($request->getHeaders());
        $this->curl->post($url, json_encode($request->getBody()));

        return $this->curl->getBody();
    }
}
