/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    var dfd = $.Deferred();

    /**
     * Loads the Pyw SDK object
     * @param {String} pywUrl - the url of the Pyw SDK
     */
    return function loadPywScript(pywUrl) {
        //configuration for loaded PYW script
        require.config({
            paths: {
                pywSdk: pywUrl
            },
            shim: {
                pywSdk: {
                    exports: 'pyw'
                }
            }
        });

        if (dfd.state() !== 'resolved') {
            require(['pywSdk'], function (pywObject) {
                dfd.resolve(pywObject);
            });
        }

        return dfd.promise();
    };
});
