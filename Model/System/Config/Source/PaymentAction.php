<?php

namespace PayYourWay\Pyw\Model\System\Config\Source;

class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'authorize_only' => 'Authorize Only',
            'authorize_capture' => 'Authorize and Capture'
        ];
    }
}
