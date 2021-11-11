<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;


/**
 * Used for creating/renewing the Pay Your Way
 * @api
 */
interface GenerateAccessTokenInterface {

    public function execute(): string;

}
