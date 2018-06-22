<?php

namespace Magento\Sanalpos\Model;
//use Magento\Payment\Model\InfoInterface;
//use Magento\Quote\Api\Data\PaymentInterface;
//use Magento\Sales\Api\Data\OrderPaymentInterface;
//use Magento\Sales\Model\Order\Payment;
//use Magento\Sales\Model\Order\Payment\Transaction;
//use Magento\Quote\Model\Quote;
//use Magento\Store\Model\ScopeInterface;
use const bar\foo\baz\const1;
use Magento\Sanalpos\Gateway\Config\Payment\Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlInterface;
use SanalPos\Garanti\SanalPosGaranti;
use SanalPos\Garanti\SanalPosReponseGaranti;

/**
 * Pay In Store payment method model
 */
class GarantiBank
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
        $quote = $this->checkoutSession->getQuote();
        $quote->reserveOrderId();

        $strMode                 = $this->config->getMode();
        $strApiVersion           = "v0.01";
        $strTerminalProvUserID   = $this->config->getProveUserIdGarantibank();
        $strType                 = "sales";
        $strAmount               = $quote->getBaseGrandTotal();
        $strCurrencyCode         = "949";
        $strInstallmentCount     = "2";
        $strTerminalUserID       = "XXXXXX";
        $strOrderID              = $quote->getReservedOrderId();
        $strCustomeripaddress    = $quote->getRemoteIp();
        $strcustomeremailaddress = $data['additional_data']['email'];
        $strTerminalID           = $this->config->getTerminalIdGarantibank();
        $strTerminalID_          = "0".$this->config->getTerminalIdGarantibank();
        $strTerminalMerchantID   = $this->config->getMerchantIdGarantibank();
        $strStoreKey             = $this->config->getStoreKeyGarantibank();
        $strProvisionPassword    = $this->config->getProvPasswordGarantibank();
        $strSuccessURL           = $this->urlBuilder->getUrl('sanalpos/action/response');
        $strErrorURL             = $this->urlBuilder->getUrl('sanalpos/action/response');
        $SecurityData            = strtoupper(sha1($strProvisionPassword.$strTerminalID_));
        $HashData                = strtoupper(sha1($strTerminalID.$strOrderID.$strAmount.$strSuccessURL.$strErrorURL.$strType.$strInstallmentCount.$strStoreKey.$SecurityData));

        //Structure your return data so the form-builder.js can build your form correctly
        $postData = array(
            'action' => 'https://sanalposprovtest.garanti.com.tr/servlet/gt3dengine',
            'fields' => array (
                'mode'                            => $strMode,
                'apiversion'                      => $strApiVersion,
                'terminalprovuserid'              => $strTerminalProvUserID,
                'terminaluserid'                  => $strTerminalUserID,
                'terminalmerchantid'              => $strTerminalMerchantID,
                'txntype'                         => $strType,
                'txnamount'                       => $strAmount,
                'txninstallmentcount'             => $strInstallmentCount,
                'orderid'                         => $strOrderID,
                'terminalid'                      => $strTerminalID,
                'cardnumber'                      => $data['additional_data']['cc_number'],
                'cardcvv2'                        => $data['additional_data']['cc_cid'],
                'cardexpiredateyear'              => substr($data['additional_data']['cc_exp_year'],-2),
                'cardexpiredatemonth'             => $data['additional_data']['cc_exp_month'],
                'secure3dhash'                    => $HashData,
                'txncurrencycode'                 => $strCurrencyCode,
                'successurl'                      => $strSuccessURL,
                'errorurl'                        => $strErrorURL,
                'customeripaddress'               => $strCustomeripaddress,
                'customeremailaddress'            => $strcustomeremailaddress,
                'lang'                            => 'tr',
                'bank_type'                       => $data['additional_data']['bank_type'],
                'code'                            => $data['method'],
                'secure3dsecuritylevel'           => '3D',
                'Ecom_Payment_Card_Number'        => $data['additional_data']['cc_number'],
                'Ecom_Payment_Card_ExpDate_Month' => $data['additional_data']['cc_exp_month'],
                'Ecom_Payment_Card_ExpDate_Year'  => substr($data['additional_data']['cc_exp_year'],-2),
                'Ecom_Payment_Card_Cv2'           => $data['additional_data']['cc_cid'],

            )
        );

        $result = $this->jsonFactory->create();
        return $result->setData($postData);
    }

    public function capture($response)
    {
        //echo "<pre>";
        //var_dump($response);die;

        $quote        = $this->checkoutSession->getQuote();

        $merchantId   = $this->config->getMerchantIdGarantibank();
        $terminalId   = $this->config->getTerminalIdGarantibank();
        $provUserId   = $this->config->getProveUserIdGarantibank();
        $provPassword = $this->config->getProvPasswordGarantibank();

        $garanti      = new SanalPosGaranti($merchantId, $terminalId, $provUserId, $provPassword, $provUserId);
        $garanti->setCard($response['Ecom_Payment_Card_Number'], $response['Ecom_Payment_Card_ExpDate_Month'], $response['Ecom_Payment_Card_ExpDate_Year'], $response['Ecom_Payment_Card_Cv2']);
        $garanti->setOrder($response['oid'], $response['customeremailaddress'], $response['txnamount']);
        $garanti->setMode($this->config->getMode());

        $result = new SanalPosReponseGaranti($garanti->pay());
        echo "<pre>";
        var_dump($result);die;
    }

}