/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
define([
    'squire',
    'ko'
], function (Squire, ko) {
    'use strict';

    describe("PayYourWay_Pyw/js/view/payment/method-renderer/payyourway", function () {
        var injector = new Squire(),
            mocks = {
                'Magento_Checkout/js/model/quote': {
                    billingAddress: ko.observable(),
                    shippingAddress: ko.observable(),
                    paymentMethod: ko.observable(),
                    isVirtual: jasmine.createSpy().and.returnValue(false),
                    totals: ko.observable('base_grand_total', 199),
                },
                'Magento_Checkout/js/checkout-data': {
                    getSelectedBillingAddress: jasmine.createSpy(),
                    getBillingAddressFromData: jasmine.createSpy(),
                    getNewCustomerBillingAddress: jasmine.createSpy()
                },
                'Magento_Checkout/js/model/payment-service': {
                    getAvailablePaymentMethods: jasmine.createSpy().and.returnValue(true)
                },
                'Magento_Customer/js/model/address-list': {
                    some: jasmine.createSpy().and.returnValue(true)
                },
        },
            obj;

        beforeAll(function (done) {
            window.preparePayment = function () {}
            window.checkoutConfig = {
                payment: {
                    payyourway: {}
                }
            };
            injector.mock(mocks);
            injector.require(['PayYourWay_Pyw/js/view/payment/method-renderer/payyourway'], function (instance) {
                obj = new instance({
                    item: {
                        method: 'payyourway'
                    }
                });
                done();
            });
        });

        afterEach(function () {
            try {
                injector.clean();
                injector.remove();
            } catch (e) {
            }
        });

        describe('Check PayYourWay initialization', function () {
            it('payyourway openPayYourWay is defined', function () {
                expect(obj.hasOwnProperty('openPayYourWay')).toBeDefined();
            });

            it('payyourway loadPayYourWay is defined', function () {
                expect(obj.hasOwnProperty('loadPayYourWay')).toBeDefined();
            });

            it('payyourway popup is defined', function () {
                expect(obj.hasOwnProperty('openPopup')).toBeDefined();
            });

            it('should call openPopup() as expected', function () {
                spyOn(obj, 'openPopup');
                obj.openPopup();
                expect(obj.openPopup).toHaveBeenCalled();
            });
        });
    });
});
