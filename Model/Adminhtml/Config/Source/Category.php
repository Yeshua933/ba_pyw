<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model\Adminhtml\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Category implements ArrayInterface
{

    /**
     * Possible environment types
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 0,
                'label' => 'N/A',
            ],
            [
                'value' => 1,
                'label' => 'Category 1',
            ],
            [
                'value' => 2,
                'label' => 'Category 2'
            ]
        ];
    }
}
