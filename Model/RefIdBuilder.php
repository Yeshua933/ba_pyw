<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use PayYourWay\Pyw\Api\ConfigInterface;
use PayYourWay\Pyw\Api\RefIdBuilderInterface;

class RefIdBuilder implements RefIdBuilderInterface
{
    private ConfigInterface $config;

    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    public function buildRefId(
        string $clientId,
        string $accessToken,
        string $requestorId,
        int $timestamp,
        string $transactionId,
        string $userId = '',
        bool $sandbox = false,
        string $secretCode = ''
    ): ?string {

        if ($sandbox) {
            $secretCode = '30820122300D06092A864886F70D01010105000382010F003082010A0282010100EE9FBD3481500ECB9F5EFEE0971A68C746E8A2F7F3A76C2407853B84188DA1B2DD20C2499C5AEE8FFE8AB3A9F1DBA2B3B53EC2C2009E2E3B33ABA8B9EBF5A106C555BFED9F6DED940915F77EB82606EC72115F827F6296417D83A2BBE3CFA3EB9EE41A5203BBBE88CD4AD7D626CED0C711021996547BBE43A0CDACF5CB6D44AC8E73FE5B2CA56D22341942EABE822024E87DA1E4F2BE5B8C3E7930DFF6711448EC3F274334A9837AA81357F7709070DCEBA6738B441612F1DE49876D10953815A76947609D43DEE16C1A6146B21461A35032FB3584C87D0CC80CF8228CCCA86C76220947C6980D5350769AE098E18C0AFA4B30E87FC2548415AC97B8AEA42E6F0203010001'; //phpcs:ignore
            $clientId = 'PYW';
            $requestorId = 'PSYW';
        }
        /**
         * Format of refId: client_id~~access_token~~requestorId~~timestamp~~transactionId~~userId
         */
        $refId = $clientId ."~~". $accessToken ."~~". $requestorId
            ."~~". $timestamp ."~~". $transactionId ."~~". $userId;

        return $this->getEncrypt($refId, $secretCode);
    }

    private function getEncrypt(string $refId, string $secretCode = ''): ?string
    {
        if ($secretCode !== '') {
            return $this->encrypt($refId, $secretCode);
        }
        $secretCode = $this->config->getSecretKey();
        if ($secretCode !== null) {
            return $this->encrypt($refId, $secretCode);
        }
        return null;
    }

    public function encrypt(string $value, string $key)
    {
        return openssl_encrypt($value, 'AES-128-ECB', $key);
    }
}
