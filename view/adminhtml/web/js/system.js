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

        if (env_id === 'sandbox') {
            client_name = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-name-sb-value"]').val();
            email = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-email-sb-value"]').val();
            client_id = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-client-id-sb-value"]').val();
            phone_number = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-phone-sb-value"]').val();
            public_key = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-public-key-sb-value"]').val();
            private_key = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-private-key-sb-value"]').val();
        } else {
            client_name = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-name-pr-value"]').val();
            email = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-email-pr-value"]').val();
            client_id = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-client-id-pr-value"]').val();
            phone_number = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-merchant-phone-pr-value"]').val();
            public_key = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-public-key-pr-value"]').val();
            private_key = $('[data-ui-id="text-groups-payyourway-groups-settings-fields-private-key-pr-value"]').val();
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

        if (!client_id) {
            errors.push($t("Please enter a Merchant ID"));
        }

        if (!client_name) {
            errors.push($t('Please enter a Client Name'));
        }

        if (!client_id) {
            errors.push($t("Please enter a Merchant ID"));
        }

        if (!email) {
            errors.push($t("Please enter an Email"));
        }

        if (!phone_number) {
            errors.push($t("Please enter a Phone number"));
        }

        if (errors.length > 0) {
            alert({
                title: $t('Payyourway Credential Validation Failed'),
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
            email: email
        }).done(function () {
            $('<div class="message message-success payyourway-registration-success-message">' + $t("Registered Client Successfully") + '</div>').insertAfter(self);
        }).fail(function () {
            alert({
                title: $t('Payyourway Registration Failed'),
                content: $t('Something went wrong')
            });
        }).always(function () {
            $(self).text($t("Validate Credentials")).attr('disabled', false);
        });
    }
});
