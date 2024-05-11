<?php
namespace Custom\Loyalitypoints\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Custom\Loyalitypoints\Helper\Data as LoyaltyPointsHelper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ConvertPointsToCoupons extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $loyaltyPointsHelper;
    protected $customerSession;
    protected $storeManager;
    protected $salesRuleFactory;
    protected $transportBuilder;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        LoyaltyPointsHelper $loyaltyPointsHelper,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        RuleFactory $salesRuleFactory,
        TransportBuilder $transportBuilder
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->loyaltyPointsHelper = $loyaltyPointsHelper;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->salesRuleFactory = $salesRuleFactory;
        $this->transportBuilder = $transportBuilder;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = ['success' => false, 'error' => ''];
        $customerId = $this->customerSession->getCustomerId();
        $loyaltyPoints = $this->loyaltyPointsHelper->getLoyaltyPointsByCustomerId($customerId);
    
        if ($loyaltyPoints && $loyaltyPoints->getId()) {
            $pointsToConvert = (int)$this->getRequest()->getParam('points', 0);
            if ($pointsToConvert <= 0 || $pointsToConvert > $loyaltyPoints->getPoints()) {
                $result['error'] = __('Invalid points amount.');
            } else {
                try {
                    $couponCode = $this->generateCoupon($pointsToConvert);
    
                    $senderCustomer = $this->customerSession->getCustomer();
                    $senderName = $senderCustomer->getName();
    
                    $recipientEmail = $this->getRequest()->getParam('recipient_email');
                    $this->sendCouponEmail($senderName, $couponCode, $recipientEmail, $pointsToConvert);
    
                    $this->loyaltyPointsHelper->updateLoyaltyPoints($customerId, -$pointsToConvert);
    
                    $result['success'] = true;
                    $result['remaining_points'] = $loyaltyPoints->getPoints() - $pointsToConvert;
                    $this->messageManager->addSuccessMessage(__('Coupon generated successfully.'));
                    $this->_getSession()->setRedirectUrl($this->_url->getUrl('customer/account/index'));
                } catch (LocalizedException $e) {
                    $result['error'] = $e->getMessage();
                    $this->messageManager->addErrorMessage($result['error']);
                }
            }
        } else {
            $result['error'] = __('No loyalty points available.');
            $this->messageManager->addErrorMessage($result['error']);
        }
    
        if ($result['success']) {
            return $this->_redirect('customer/account/index');
        }
    
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }

    protected function _getSession()
    {
        return $this->_objectManager->get(\Magento\Customer\Model\Session::class);
    }

    protected function generateCoupon($points)
    {
        $ruleData = [
            'name' => 'Loyalty Points Coupon',
            'description' => 'Coupon generated from loyalty points conversion',
            'is_active' => 1,
            'website_ids' => [$this->storeManager->getStore()->getWebsiteId()],
            'customer_group_ids' => [0, 1],
            'coupon_type' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC,
            'coupon_code' => 'LOYALTY_COUPON_' . uniqid(),
            'uses_per_coupon' => 1,
            'uses_per_customer' => 1,
            'from_date' => null,
            'to_date' => null,
            'sort_order' => 0,
            'simple_action' => \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION,
            'discount_amount' => $points,
            'discount_step' => 0,
            'apply_to_shipping' => 0,
            'is_rss' => 0
        ];

        $rule = $this->salesRuleFactory->create();
        $rule->setData($ruleData);
        $rule->save();

        return $rule->getCouponCode();
    }

    protected function sendCouponEmail($senderName, $couponCode, $recipientEmail, $couponAmount)
    {
        try {
            $customer = $this->customerSession->getCustomerDataObject();
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Customer not found.'));
        }

        $templateVars = [
            'sender_name' => $senderName,
            'coupon_code' => $couponCode,
            'coupon_amount' => $couponAmount
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('loyaltypoints_coupon_email_template')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $this->storeManager->getStore()->getId()])
            ->setTemplateVars($templateVars)
            ->setFrom(['email' => 'sales@example.com', 'name' => $senderName])
            ->addTo($recipientEmail)
            ->getTransport();

        $transport->sendMessage();
    }
}