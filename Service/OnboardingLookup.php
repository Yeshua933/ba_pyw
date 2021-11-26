<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service;

use Magento\Framework\HTTP\ClientInterface;
use PayYourWay\Pyw\Api\OnboardingLookupInterface;
use PayYourWay\Pyw\Api\OnboardingRequestInterface;
use PayYourWay\Pyw\Model\Adminhtml\Config\Source\Environment;

class OnboardingLookup implements OnboardingLookupInterface
{
    private ClientInterface $curl;
    private const URL_UAT = 'https://oauthweb.uat.telluride.transformco.com/oauthUI/register/registerClient';
    private const URL_PRD = 'https://oauthweb.telluride.transformco.com/oauthUI/register/registerClient';

    public function __construct(
        ClientInterface $curl
    ) {
        $this->curl = $curl;
    }

    public function execute(
        OnboardingRequestInterface $request
    ): string {
        $url = $request->getEnvironment() === Environment::ENVIRONMENT_SANDBOX ?
            self::URL_UAT :
            self::URL_PRD;

        $this->curl->setHeaders($request->getHeaders());
        $this->curl->post($url, json_encode($request->getBody()));

        return $this->curl->getBody();
    }
}
