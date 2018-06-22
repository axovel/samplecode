<?php
 
namespace Magento\Sanalpos\Model;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;
 
/**
 * Pay In Store payment method model
 */
class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
 	const CODE = 'magento_sanalpos';
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'magento_sanalpos';

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
 	{
 		return $this;
 	}
}