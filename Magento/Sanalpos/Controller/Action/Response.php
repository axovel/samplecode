<?php 
namespace Magento\Sanalpos\Controller\Action;

use Magento\Sanalpos\Gateway\Config\Payment\Config;
use Magento\Sanalpos\Model\Helper;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Sanalpos\Model\AkBank;
use Magento\Sanalpos\Model\GarantiBank;
use Magento\Sanalpos\Model\IsBank;

/**
 * Class PlaceOrder
 */
class Response extends AbstractAction
{
    /**
     * @var Helper\OrderPlace
     */
    private $orderPlace;

    /**
     * Logger for exception details
     *
     * @var LoggerInterface
     */
    private $logger;

    private $cart;

    /**
     * Response constructor.
     * @param Context $context
     * @param Config $config
     * @param Session $checkoutSession
     * @param Cart $cart
     * @param Helper\OrderPlace $orderPlace
     * @param LoggerInterface|null $logger
     * @param GarantiBank $garantiBank
     * @param IsBank $isBank
     * @param AkBank $akBank
     */
    public function __construct(
        Context $context,
        Config $config,
        Session $checkoutSession,
        Cart $cart,
        Helper\OrderPlace $orderPlace,
        LoggerInterface $logger = null,
        GarantiBank $garantiBank,
        IsBank $isBank,
        AkBank $akBank
    ) {
        parent::__construct($context, $config, $checkoutSession);
        $this->orderPlace = $orderPlace;
        $this->cart = $cart;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->akBank = $akBank;
        $this->isBank = $isBank;
        $this->garantBank = $garantiBank;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute()
    {
        $response = $this->getRequest()->getPostValue();

        if ($response['bank_type'] == 'akbank') {
            $this->akBank->capture($response);
        }

        if ($response['bank_type'] == 'isbank') {
            $this->isBank->capture($response);
        }

        if ($response['bank_type'] == 'garantibank') {
            $this->garantBank->capture($response);
        }

        echo "<pre>";
        var_dump($response);die;

        //echo $this->config->getClientIdAkbank();die;

        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data['mdStatus'] == '1') {

            $agreement = array_keys($this->getRequest()->getPostValue());
            $quote = $this->checkoutSession->getQuote();
            $quote->getPayment()->importData(['method' => 'magento_sanalpos']);
            $quote->collectTotals()->save();

            try {

                $this->validateQuote($quote);
                $this->orderPlace->execute($quote, $agreement);

                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                return $resultRedirect->setPath('checkout/onepage/success', ['_secure' => true]);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }

            return $resultRedirect->setPath('checkout/cart', ['_secure' => true]);
        } else {

            $this->messageManager->addError($data['mdErrorMsg']);
            return $resultRedirect->setPath('checkout/cart', ['_secure' => true]);
        }
        
    }
}
