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
                'label' => 'Other',
            ],
            [
                'value' => 1,
                'label' => 'Travel',
            ],
            [
                'value' => 2,
                'label' => 'Grocery'
            ],
            [
                'value' => 3,
                'label' => 'Restaurant'
            ],
            [
                'value' => 4,
                'label' => 'Gas Station'
            ],
        ];
    }
}
