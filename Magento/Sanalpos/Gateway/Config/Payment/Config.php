<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sanalpos\Gateway\Config\Payment;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\CcConfig;

/**
 * Class Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ACTIVE  = 'active';

    const KEY_TITLE   = 'title';

    const METHOD_CODE = 'magento_sanalpos';

    const MODE        = 'TEST';

    const API_VERSION = 'v0.01';

    const TXN_TYPE    = 'sales';


    /* AKBank Contact Variable */
    const KEY_CLIENT_ID_AKBANK    = 'akbank/clinet_id';
    const KEY_API_USER_AKBANK     = 'akbank/api_user';
    const KEY_API_PASSWORD_AKBANK = 'akbank/api_password';
    const KEY_EMI_AKBANK          = 'akbank/emi';
    const KEY_STORE_KEY_AKBANK    = 'akbank/store_key';

    /* ISBank Contact Variable */
    const KEY_CLIENT_ID_ISBANK    = 'isbank/clinet_id';
    const KEY_API_USER_ISBANK     = 'isbank/api_user';
    const KEY_API_PASSWORD_ISBANK = 'isbank/api_password';
    const KEY_EMI_ISBANK          = 'isbank/emi';
    const KEY_STORE_KEY_ISBANK    = 'isbank/store_key';

    /* GarantiBank Contact Variable */
    const KEY_MERCHANT_ID_GARANTI   = 'garantibank/merchant_id';
    const KEY_TERMINAL_ID_GARANTI   = 'garantibank/terminal_id';
    const KEY_PROV_USER_ID_GARANTI  = 'garantibank/prov_user_id';
    const KEY_PROV_PASSWORD_GARANTI = 'garantibank/prov_password';
    const KEY_EMI_GARANTI           = 'garantibank/emi';
    const KEY_STORE_KEY_GARANTI     = 'garantibank/store_key';

    /**
     * @var CcConfig
     */
    private $ccConfig;

    /**
     * @var array
     */
    private $icon = [];

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param CcConfig $ccConfig
     * @param string $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CcConfig $ccConfig,
        $methodCode = self::METHOD_CODE,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->ccConfig = $ccConfig;
    }

    /**
     * Get Payment configuration status
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }

    public function getMode()
    {
       return $this->getValue(self::MODE);
    }

    public function getTxnType()
    {
        return $this->getValue(self::TXN_TYPE);
    }

    public function getApiVersion()
    {
        return $this->getValue(self::API_VERSION);
    }

    public function getClientIdAkbank()
    {
        return $this->getValue(self::KEY_CLIENT_ID_AKBANK);
    }

    public function getApiUserAkbank()
    {
        return $this->getValue(self::KEY_API_USER_AKBANK);
    }

    public function getApiPasswordAkbank()
    {
        return $this->getValue(self::KEY_API_PASSWORD_AKBANK);
    }

    public function getEmiAkbank()
    {
        return $this->getValue(self::KEY_EMI_AKBANK);
    }

    public function getStoreKeyAkbank()
    {
        return $this->getValue(self::KEY_STORE_KEY_AKBANK);
    }


    public function getClientIdIsbank()
    {
        return $this->getValue(self::KEY_CLIENT_ID_ISBANK);
    }

    public function getApiUserIsbank()
    {
        return $this->getValue(self::KEY_API_USER_ISBANK);
    }

    public function getApiPasswordIsbank()
    {
        return $this->getValue(self::KEY_API_PASSWORD_ISBANK);
    }

    public function getEmiIsbank()
    {
        return $this->getValue(self::KEY_EMI_ISBANK);
    }

    public function getStoreKeyIsbank()
    {
        return $this->getValue(self::KEY_STORE_KEY_ISBANK);
    }

    public function getMerchantIdGarantibank()
    {
        return $this->getValue(self:: KEY_MERCHANT_ID_GARANTI);
    }

    public function getTerminalIdGarantibank()
    {
        return $this->getValue(self:: KEY_TERMINAL_ID_GARANTI);
    }

    public function getProveUserIdGarantibank()
    {
        return $this->getValue(self:: KEY_PROV_USER_ID_GARANTI);
    }

    public function getProvPasswordGarantibank()
    {
        return $this->getValue(self:: KEY_PROV_PASSWORD_GARANTI);
    }

    public function getStoreKeyGarantibank()
    {
        return $this->getValue(self:: KEY_STORE_KEY_GARANTI);
    }

    public function getEmiGarantibank()
    {
        return $this->getValue(self::KEY_EMI_GARANTI);
    }
}
