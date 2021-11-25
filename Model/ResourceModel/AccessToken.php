<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AccessToken extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('pyw_access_token', 'entity_id');
    }
}
