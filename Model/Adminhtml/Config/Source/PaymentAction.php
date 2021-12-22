<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Model\Adminhtml\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentAction implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            'authorize_capture' => 'Authorize and Capture'
        ];
    }
}
