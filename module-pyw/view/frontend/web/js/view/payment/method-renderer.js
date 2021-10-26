define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'payyourway',
                component: 'PayYourWay_Pyw/js/view/payment/method-renderer/payyourway'
            },
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
