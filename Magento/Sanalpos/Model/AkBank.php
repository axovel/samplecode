<?php

namespace Magento\Sanalpos\Model;
//use Magento\Payment\Model\InfoInterface;
//use Magento\Quote\Api\Data\PaymentInterface;
//use Magento\Sales\Api\Data\OrderPaymentInterface;
//use Magento\Sales\Model\Order\Payment;
//use Magento\Sales\Model\Order\Payment\Transaction;
//use Magento\Quote\Model\Quote;
//use Magento\Store\Model\ScopeInterface;
use Magento\Sanalpos\Gateway\Config\Payment\Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlInterface;
use SanalPos\Est\SanalPosEst;
use SanalPos\Est\SanalPosResponseEst;

/**
 * Pay In Store payment method model
 */
class AkBank
{
    /**
     * IsBank constructor.
     * @param Config $config
     * @param Session $checkoutSession
     * @param JsonFactory $jsonFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Config $config,
        Session $checkoutSession,
        JsonFactory $jsonFactory,
        UrlInterface $urlBuilder
    ){
        $this->config          = $config;
        $this->checkoutSession = $checkoutSession;
        $this->jsonFactory     = $jsonFactory;
        $this->urlBuilder      = $urlBuilder;
    }

    /**
     * @param $data
     * @return $this
     */
    public function requestBuilder($data)
    {
        $quote     = $this->checkoutSession->getQuote();
        $clientId  = $this->config->getClientIdAkbank();
        $amount    = $quote->getBaseGrandTotal();

        $quote->reserveOrderId();
        $oid       = $quote->getReservedOrderId();

        $okUrl     = $this->urlBuilder->getUrl('sanalpos/action/response');
        $failUrl   = $this->urlBuilder->getUrl('sanalpos/action/response');
        $rnd       = microtime();
        $storekey  = $this->config->getStoreKeyAkbank();

        $hashstr   = $clientId . $oid . $amount . $okUrl . $failUrl . $rnd  . $storekey;
        $hash      = base64_encode(pack('H*',sha1($hashstr)));

        //Structure your return data so the form-builder.js can build your form correctly
        $postData = array(
            'action' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
            'fields' => array (
                'pan'                             => $data['additional_data']['cc_number'],
                'cv2'                             => $data['additional_data']['cc_cid'],
                'Ecom_Payment_Card_Cv2'           => $data['additional_data']['cc_cid'],
                'Ecom_Payment_Card_Number'        => $data['additional_data']['cc_number'],
                'Ecom_Payment_Card_ExpDate_Year'  => substr($data['additional_data']['cc_exp_year'],-2),
                'Ecom_Payment_Card_ExpDate_Month' => $data['additional_data']['cc_exp_month'],
                'clientid'                        => $clientId,
                'amount'                          => $amount,
                'oid'                             => $oid,
                'okUrl'                           => $okUrl,
                'failUrl'                         => $failUrl,
                'rnd'                             => $rnd,
                'hash'                            => $hash,
                'currency'                        => '949',
                'storetype'                       => '3d',
                'lang'                            => 'tr',
                'bank_type'                       => $data['additional_data']['bank_type'],
                'taskit'                          => 1,
                'code'                            => $data['method'],
                'email'                           => $data['additional_data']['email']
            )
        );

        $result = $this->jsonFactory->create();
        return $result->setData($postData);
    }

    public function capture($response)
    {
        $quote       = $this->checkoutSession->getQuote();
//        echo "<pre>";
//        var_dump($quote->getCustomerEmail());die;

        $userName    = $this->config->getApiUserAkbank();
        $apiPassword = $this->config->getApiPasswordAkbank();
        $clientId    = $this->config->getClientIdAkbank();

        $est         = new SanalPosEst('akbank', $clientId, $userName, $apiPassword);
        $est->setCard($response['Ecom_Payment_Card_Number'], $response['Ecom_Payment_Card_ExpDate_Month'], $response['Ecom_Payment_Card_ExpDate_Year'], $response['Ecom_Payment_Card_Cv2']);
        $est->setOrder($response['oid'], $response['email'], $response['amount']);
        $est->setMode($this->config->getMode());

        $result = new SanalPosResponseEst($est->pay());
        echo "<pre>";
        var_dump($result);die;
    }
}