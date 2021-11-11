define(
    [
        'Magento_Checkout/js/view/payment/default',
        'PayYourWay_Pyw/js/pyw-sdk',
    ],
    function (Component,pywSdk) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'PayYourWay_Pyw/payment/payyourway'
            },
            paymentConfig : window.checkoutConfig.payment.payyourway,
            payyourway: null,
            pywLoaded : false,
            baseUrl:  window.BASE_URL,

            initialize : function () {
                this._super();
                if (!this.pywLoaded) {
                    this.loadPayYourWay()
                        .then(this._setPywObject.bind(this));
                }
            },

            openPayYourWay : function () {
                if (!this.pywLoaded) {
                    this.loadPayYourWay()
                        .then(this._setPywObjectAndOpen.bind(this));
                } else {
                    this.openPopup();
                }

            },

            loadPayYourWay : function (open) {
                let sdkUrl = this.paymentConfig.sdkUrl;
                return pywSdk(sdkUrl);
            },

            _setPywObject: function (pyw) {
                this.pywLoaded = true;
            },

            _setPywObjectAndOpen: function (pyw) {
                this.pywLoaded = true;
                if (this.pywLoaded) {
                    this.openPopup();
                }
            },

            openPopup : function () {
                let total = 500;
                let returnUrl = this.baseUrl + 'payyourway/checkout/return';

                preparePayment(this.paymentConfig.refid, total, returnUrl);
            }
        });
    }
);
