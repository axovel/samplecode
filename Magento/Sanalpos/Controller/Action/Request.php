<?php 
namespace Magento\Sanalpos\Controller\Action;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;
use Magento\Sanalpos\Model\AkBank;
use Magento\Sanalpos\Model\GarantiBank;
use Magento\Sanalpos\Model\IsBank;

class Request extends \Magento\Framework\App\Action\Action
{
    /**
     * Request constructor.
     * @param Context $context
     * @param GarantiBank $garantiBank
     * @param IsBank $isBank
     * @param AkBank $akBank
     */
    public function __construct(
            Context $context,
            GarantiBank $garantiBank,
            IsBank $isBank,
            AkBank $akBank
        ) {
            parent::__construct($context);
            $this->akBank = $akBank;
            $this->isBank = $isBank;
            $this->garantBank = $garantiBank;
        }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if ($data['additional_data']['bank_type'] == 'akbank') {
            return $this->akBank->requestBuilder($data);
        }

        if ($data['additional_data']['bank_type'] == 'garantibank') {
            return $this->garantBank->requestBuilder($data);
        }

        if ($data['additional_data']['bank_type'] == 'isbank') {
            return $this->isBank->requestBuilder($data);
        }


//        echo "<pre>";
//        //$this->checkoutSession->getQuote()->reserveOrderId();
//        $data = $this->getRequest()->getPost();
//        var_dump($data['additional_data']);die;

        //return data in JSON format
    }
}