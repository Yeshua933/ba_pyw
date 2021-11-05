require(['jquery', 'Magento_Ui/js/modal/alert', 'mage/translate', 'domReady!'], function ($, alert, $t) {
    window.pywValidator = function (endpoint, env_id) {
        env_id = $('[data-ui-id="' + env_id + '"]').val();

        var merch_id = '', public_id = '', private_id = '';

        if (env_id === 'sandbox') {
            merch_id = $('[data-ui-id="text-groups-payyourway-fields-client-id-sb-value"]').val();
            public_id = $('[data-ui-id="textarea-groups-payyourway-fields-public-key-sb-value"]').val();
            private_id = $('[data-ui-id="textarea-groups-payyourway-fields-private-key-sb-value"]').val();
        } else {
            merch_id = $('[data-ui-id="text-groups-payyourway-fields-client-id-pr-value"]').val();
            public_id = $('[data-ui-id="textarea-groups-payyourway-fields-public-key-pr-value"]').val();
            private_id = $('[data-ui-id="textarea-groups-payyourway-fields-private-key-pr-value"]').val();
        }

        // /* Remove previous success message if present */
        // if ($(".braintree-credentials-success-message")) {
        //     $(".braintree-credentials-success-message").remove();
        // }

        /* Basic field validation */
        var errors = [];

        if (!env_id || env_id !== 'sandbox' && env_id !== 'production') {
            errors.push($t("Please select an Environment"));
        }

        if (!merch_id) {
            errors.push($t("Please enter a Merchant ID"));
        }

        if (!public_id) {
            errors.push($t('Please enter a Public Key'));
        }

        if (!private_id) {
            errors.push($t('Please enter a Private Key'));
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
            merchant_id: merch_id,
            public_key: public_id,
            private_key: private_id
        }).done(function () {
            $('<div class="message message-success braintree-credentials-success-message">' + $t("Your credentials are valid.") + '</div>').insertAfter(self);
        }).fail(function () {
            alert({
                title: $t('Payyourway Credential Validation Failed'),
                content: $t('Your Braintree Credentials could not be validated. Please ensure you have selected the correct environment and entered a valid Merchant ID, Public Key and Private Key.')
            });
        }).always(function () {
            $(self).text($t("Validate Credentials")).attr('disabled', false);
        });
    }
    // disablePayLaterMessages();
});
