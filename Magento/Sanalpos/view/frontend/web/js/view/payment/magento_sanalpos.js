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
        type: 'magento_sanalpos',
        component: 'Magento_Sanalpos/js/view/payment/method-renderer/magento_sanalpos'
      }
    );
    /** Add view logic here if needed */
    return Component.extend({});
  }
);