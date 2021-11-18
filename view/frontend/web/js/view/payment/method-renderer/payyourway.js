define(
    [
        'Magento_Checkout/js/view/payment/default',
        'PayYourWay_Pyw/js/pyw-sdk',
        'Magento_Checkout/js/model/quote',
        'mage/url'
    ],
    function (Component, pywSdk, quote, urlBuilder) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'PayYourWay_Pyw/payment/payyourway'
            },
            paymentConfig : window.checkoutConfig.payment.payyourway,
            payyourway: null,
            pywLoaded : false,
            baseUrl:  window.BASE_URL,
            isPopupTriggered: false,

            initialize : function () {
                this._super();

                window.addEventListener('hashchange', _.bind(this.handleHash, this));

                if (!this.pywLoaded) {
                    this.loadPayYourWay()
                        .then(this._setPywObject.bind(this));
                }
            },

            openPayYourWay : function () {
                document.getElementById("overlay").style.display = "block";
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
                let grandTotal = parseFloat(this.getGrandTotal());
                let returnUrl = this.baseUrl + 'payyourway/checkout/return';
                this.isPopupTriggered = true;

                preparePayment(this.paymentConfig.refid, grandTotal, returnUrl);
            },

            getGrandTotal : function () {
                return quote.totals()['base_grand_total'];
            },

            handleHash: function () {
                let hash = window.location.hash;

                if (this.isPopupTriggered && hash !== '#payment') {
                    window.location.href = urlBuilder.build('payyourway/checkout/return');
                }
            }
        });
    }
);
