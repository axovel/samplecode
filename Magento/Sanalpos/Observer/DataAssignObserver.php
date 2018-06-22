<?php

namespace Magento\Sanalpos\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\DataObject;
use Magento\Payment\Model\InfoInterface;
use Magento\Framework\Exception\LocalizedException;

class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $additionalData = new DataObject($additionalData);

        $paymentMethod = $this->readMethodArgument($observer);

        $payment = $observer->getPaymentModel();
        if (!$payment instanceof InfoInterface) {
            $payment = $paymentMethod->getInfoInstance();
        }

        if (!$payment instanceof InfoInterface) {
            throw new LocalizedException(__('Payment model does not provided.'));
        }

        $payment->setCcLast4($additionalData->getData('cc_last4'));
        $payment->setCcType($additionalData->getData('cc_type'));
        $payment->setCcExpMonth($additionalData->getData('cc_exp_month'));
        $payment->setCcExpYear($additionalData->getData('cc_exp_year'));
        $payment->setCcNumber($additionalData->getData('cc_number'));
        $payment->setCcCid($additionalData->getData('cc_cid'));
        $payment->setAdditionalInformation('bank_type', $additionalData->getData('bank_type'));
    }
}
