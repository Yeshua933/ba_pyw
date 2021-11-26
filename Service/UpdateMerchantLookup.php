<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Service;

use Magento\Framework\HTTP\ClientInterface;
use PayYourWay\Pyw\Api\UpdateMerchantLookupInterface;
use PayYourWay\Pyw\Api\UpdateMerchantRequestInterface;
use PayYourWay\Pyw\Model\Adminhtml\Config\Source\Environment;

class UpdateMerchantLookup implements UpdateMerchantLookupInterface
{
    private ClientInterface $curl;
    private const URL_UAT = 'https://checkout.uat.telluride.shopyourway.com/tell/api/merchant/v1/create';
    private const URL_PRD = 'https://checkout.telluride.shopyourway.com/tell/api/merchant/v1/create';

    public function __construct(
        ClientInterface $curl
    ) {
        $this->curl = $curl;
    }

    public function execute(
        UpdateMerchantRequestInterface $request
    ): string {
        $url = $request->getEnvironment() === Environment::ENVIRONMENT_SANDBOX ?
            self::URL_UAT :
            self::URL_PRD;

        $this->curl->setHeaders($request->getHeaders());
        $this->curl->post($url, json_encode($request->getBody()));

        return $this->curl->getBody();
    }
}
