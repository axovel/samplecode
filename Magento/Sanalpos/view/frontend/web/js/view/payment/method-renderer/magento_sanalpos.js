define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Magento_Checkout/js/action/place-order',
        'Pmclain_Stripe/js/action/save-payment-information',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'Magento_Checkout/js/action/redirect-on-success',
        'Magento_Vault/js/view/payment/vault-enabler',
        'Magento_Checkout/js/model/quote',
        'Magento_Ui/js/modal/alert',
        'Magento_Customer/js/customer-data',
        'mage/url',
        'Magento_Sanalpos/js/model/form-builder',
        'ko'
        //'https://js.stripe.com/v3/'
    ],
    function (
        $,
        Component,
        placeOrderAction,
        savePaymentAction,
        fullScreenLoader,
        additionalValidators,
        validator,
        redirectOnSuccessAction,
        VaultEnabler,
        quote,
        alert,
        customerData,
        url,
        formBuilder,
        ko

    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_Sanalpos/payment/form'
            },

            initialize: function() {
                this._super();
            },

            placeOrder: function(data, event) {

                fullScreenLoader.startLoader();
                var custom_controller_url = url.build('sanalpos/action/request'); //your custom controller url
                $.post(custom_controller_url, this.getData(), 'json')
                    .done(function (response) {
                        customerData.invalidate(['cart']);
                        formBuilder(response).submit(); //this function builds and submits the form
                    })
                    .fail(function (response) {
                        errorProcessor.process(response, this.messageContainer);
                    })
                    .always(function () {
                        fullScreenLoader.stopLoader();
                    });
            },

            _placeOrder: function() {
                var self = this,
                    placeOrder = placeOrderAction(self.getData(), self.messageContainer);

                $.when(placeOrder).done(function() {
                    if (self.redirectAfterPlaceOrder) {
                        redirectOnSuccessAction.execute();
                    }
                }).fail(function() {
                    fullScreenLoader.stopLoader();
                    self.isPlaceOrderActionAllowed(true);
                });
            },

            getCode: function() {
                return 'magento_sanalpos';
            },

            isActive: function() {
                return true;
            },

            getData: function() {
                var data = this._super();
                data.additional_data.bank_type = $('input[name="payment\\[bank_type\\]"]:checked').val();
                data.additional_data.emi       = $('input[name="payment\\[emi\\]"]:checked').val();
                data.additional_data.email     = this.getEmail();
                return data;
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },

            getEmail: function() {
                if(quote.guestEmail) {
                    return quote.guestEmail;
                }
                return window.checkoutConfig.customerData.email;
            },

            getTaskit: function (data) {

                var bankType = $('input[name="payment\\[bank_type\\]"]:checked').val();
                var grandTotal = quote.totals().grand_total;

                if (bankType == 'akbank') {
                    data.additional_data.taskit       = window.checkoutConfig.payment[this.getCode()].emiAkbank;
                    data.additional_data.taskitAmount = (grandTotal/window.checkoutConfig.payment[this.getCode()].emiAkbank).toFixed(2);
                    console.log(data);
                    return data;
                }

                if (bankType == 'isbank') {
                    data.additional_data.taskit  = window.checkoutConfig.payment[this.getCode()].emiIsbank;
                    data.additional_data.taskitAmount  = (grandTotal/window.checkoutConfig.payment[this.getCode()].emiIsbank).toFixed(2);
                    console.log(data);
                    return data;
                }

                if (bankType == 'garantibank') {
                    data.additional_data.taskit = window.checkoutConfig.payment[this.getCode()].emiGarantibank;
                    data.additional_data.taskitAmount = (grandTotal/window.checkoutConfig.payment[this.getCode()].emiGarantibank).toFixed(2);
                    console.log(data);
                    return data;
                }

                return 0;
            }
        });
    }
);
