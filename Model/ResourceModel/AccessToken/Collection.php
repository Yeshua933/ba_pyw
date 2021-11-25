<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model\ResourceModel\AccessToken;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PayYourWay\Pyw\Model\AccessToken as Model;
use PayYourWay\Pyw\Model\ResourceModel\AccessToken as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
