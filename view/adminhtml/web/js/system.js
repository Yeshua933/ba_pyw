/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
require(['jquery', 'Magento_Ui/js/modal/alert', 'mage/translate', 'domReady!'], function ($, alert, $t) {
    window.pywValidator = function (endpoint, env_id) {
        env_id = $('[data-ui-id="' + env_id + '"]').val();

        var client_id = '';
        var phone_number = '';
        var client_name = '';
        var email = '';
        var public_key = '';
        var private_key = '';
        var address = '';
        var category = '';


        if (env_id === 'sandbox') {
            client_name = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-name-sb-value"]').val();
            email = $('[data-ui-id="text-groups-payyourway-groups-register-fields-merchant-email-sb-value"]').val();
            client_id = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-client-id-sb-value"]').val();
            phone_number = $('[data-ui-id="text-groups-payyourway-groups-register-fields-merchant-phone-sb-value"]').val();
            public_key = $('[data-ui-id="text-groups-payyourway-groups-register-fields-public-key-sb-value"]').val();
            private_key = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-private-key-sb-value"]').val();
            address = $('[data-ui-id="text-groups-payyourway-groups-register-fields-merchant-address-sb-value"]').val();
            category = $('[data-ui-id="select-groups-payyourway-groups-register-fields-merchant-category-sb-value"]').val();
        } else {
            client_name = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-name-pr-value"]').val();
            email = $('[data-ui-id="text-groups-payyourway-groups-register-fields-merchant-email-pr-value"]').val();
            client_id = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-client-id-pr-value"]').val();
            phone_number = $('[data-ui-id="text-groups-payyourway-groups-register-fields-merchant-phone-pr-value"]').val();
            public_key = $('[data-ui-id="text-groups-payyourway-groups-register-fields-public-key-pr-value"]').val();
            private_key = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-private-key-pr-value"]').val();
            address = $('[data-ui-id="text-groups-payyourway-groups-register-fields-merchant-address-pr-value"]').val();
            category = $('[data-ui-id="select-groups-payyourway-groups-register-fields-merchant-category-pr-value"]').val();
        }

        /* Remove previous success message if present */
        var registration_message = $(".message message-success payyourway-registration-success-message");
        if (registration_message) {
            registration_message.remove();
        }

        /* Basic field validation */
        var errors = [];

        if (!env_id || env_id !== 'sandbox' && env_id !== 'production') {
            errors.push($t("Please select an Environment"));
        }

        if (!client_id || client_id === '') {
            errors.push($t("Please enter a Client ID"));
        }

        if (!client_name || client_name === '') {
            errors.push($t('Please enter a Merchant Name'));
        }

        if (!client_id || client_id === '') {
            errors.push($t("Please enter a Merchant ID"));
        }

        if (!email || email === '') {
            errors.push($t("Please enter an Email"));
        }

        if (!phone_number || phone_number === '') {
            errors.push($t("Please enter a Phone number"));
        }

        if (errors.length > 0) {
            alert({
                title: $t('Please provide the following information'),
                content:  errors.join('<br />')
            });
            return false;
        }

        $(this).text($t("We're validating your credentials...")).attr('disabled', true);

        var self = this;
        $.post(endpoint, {
            environment: env_id,
            client_id: client_id,
            client_name: client_name,
            public_key: public_key,
            private_key: private_key,
            phone_number: phone_number,
            address: address,
            category: category,
            email: email
        }).done(function () {
            $('<div class="message message-success payyourway-registration-success-message">' + $t("Registered Client Successfully") + '</div>').insertAfter(self);
        }).fail(function (jqXHR) {
            alert({
                title: $t('Payyourway Registration Failed'),
                content: jqXHR.responseText
            });
        }).always(function () {
            $(self).text($t("Register")).attr('disabled', false);
        });
    }

    function generate_client_id(){
        let clientIdSb = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-client-id-sb-value"]');
        let clientIdPr = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-client-id-pr-value"]');
        let merchantNameSb = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-name-sb-value"]');
        let merchantNamePr = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-name-pr-value"]');
        let environment = $('[data-ui-id="select-groups-payyourway-groups-settings-fields-environment-value"]');
        if (environment.val() === 'sandbox') {

            let merchantName = merchantNameSb.val();
            let clientId = 'MG_'+merchantName.replace(/\s/g, '')+'_QA';
            clientIdSb.val(clientId);
        } else {

            let merchantName = merchantNamePr.val();
            let clientId = 'MG_'+merchantName.replace(/\s/g, '');
            clientIdPr.val(clientId);
        }
    }

    $('#payment_us_payyourway_settings_merchant_name_sb').on("input",function () {
        generate_client_id();

    });

    $('#payment_us_payyourway_settings_merchant_name_pr').on("input",function () {
        generate_client_id();

    });
});
