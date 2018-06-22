<?php
namespace Magento\HideCod\Plugin;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Quote\Model\Quote;
class CashOnDelivery
{

  /**
   * @var \Magento\Checkout\Model\Session
   */
   protected $_checkoutSession;

  /**
   * Constructor
   *
   * @param \Magento\Checkout\Model\Session $checkoutSession
   */
    public function __construct
    (
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession
     ) {
        $this->logger = $logger;
        $this->_checkoutSession = $checkoutSession;
        return;
    }

    public function aroundIsAvailable(\Magento\Payment\Model\Method\AbstractMethod $subject, callable $proceed)
    {
        $shippingMethod = $this->_checkoutSession->getQuote()->getShippingAddress()->getShippingMethod();
        #$this->logger->debug($shippingMethod);
        if ($shippingMethod == 'customshippingrate_ptt') {
            return false;
        }
        $result = $proceed();
        return $result;
      }
}