<?php
namespace Custom\Loyalitypoints\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Custom\Loyalitypoints\Helper\Data as LoyaltyPointsHelper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;

class SharePointsController extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $loyaltyPointsHelper;
    protected $customerSession;
    protected $storeManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        LoyaltyPointsHelper $loyaltyPointsHelper,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->loyaltyPointsHelper = $loyaltyPointsHelper;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
{
    $result = ['success' => false, 'error' => ''];
    $postData = $this->getRequest()->getPostValue();
    $storeId = $this->storeManager->getStore()->getId();
    if ($postData && isset($postData['recipient_email']) && isset($postData['points'])) {
        $senderCustomerId = $this->customerSession->getCustomerId();
        $recipientEmail = $postData['recipient_email'];
        $points = (int)$postData['points'];
        $this->storeManager->setCurrentStore($storeId);
        $senderPointsModel = $this->loyaltyPointsHelper->getLoyaltyPointsByCustomerId($senderCustomerId);
        $senderPoints = $senderPointsModel->getPoints();
        if ($points <= $senderPoints && $points > 0) {
            $this->loyaltyPointsHelper->updateLoyaltyPoints($senderCustomerId, -$points);
            $this->loyaltyPointsHelper->addLoyaltyPointsByEmail($recipientEmail, $points);
            $this->loyaltyPointsHelper->sendPointsSharingEmail($senderCustomerId, $recipientEmail, $points);
            $result['success'] = true;
            $this->messageManager->addSuccessMessage(__('Points shared successfully.'));
            $this->_getSession()->setRedirectUrl($this->_url->getUrl('customer/account/index'));
        } else {
            $result['error'] = __('You do not have enough points to send');
            $this->messageManager->addErrorMessage($result['error']);
        }
    }

    if ($result['success']) {
        return $this->_redirect('customer/index/index');
    }

    $resultJson = $this->resultJsonFactory->create();
    return $resultJson->setData($result);
}

protected function _getSession()
{
    return $this->_objectManager->get(\Magento\Customer\Model\Session::class);
}
}
