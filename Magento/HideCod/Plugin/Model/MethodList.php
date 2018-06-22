
<?php 

namespace Magento\HideCod\Plugin\Model;

class MethodList
{
    public function afterGetAvailableMethods(
        \Magento\Payment\Model\MethodList $subject,
        $availableMethods,
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        $shippingMethod = $this->getShippingMethod($quote);
        foreach ($availableMethods as $key => $method) {
            // change logic here
            if(($method->getCode() == 'cashondelivery') && ($shippingMethod == 'flatrate_flatrate')) {
                unset($availableMethods[$key]);
            }
        }

        return $availableMethods;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return string
     */
    private function getShippingMethod($quote)
    {
        if($quote) {
            return $quote->getShippingAddress()->getShippingMethod();
        }

        return '';
    }
}