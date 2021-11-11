<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model;

use PayYourWay\Pyw\Api\GenerateAccessTokenInterface;

/**
 * Used for creating/renewing the Pay Your Way access token
 * @api
 */
class GenerateAccessToken implements GenerateAccessTokenInterface {

    public function execute(): string
    {
        //TODO add the generate process; load store configuration
        return '';
    }
}
