<?php
namespace Custom\Loyalitypoints\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Custom\Loyalitypoints\Helper\Data as LoyaltyPointsHelper;
use Magento\Customer\Model\Session as CustomerSession;

class LoyaltyPoints implements ArgumentInterface
{
    /**
     * @var LoyaltyPointsHelper
     */
    private $loyaltyPointsHelper;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(
        LoyaltyPointsHelper $loyaltyPointsHelper,
        CustomerSession $customerSession
    ) {
        $this->loyaltyPointsHelper = $loyaltyPointsHelper;
        $this->customerSession = $customerSession;
    }

    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    public function getLoyaltyPoints($customerId)
{
    $logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
    $loyaltyPointsModel = $this->loyaltyPointsHelper->getLoyaltyPointsByCustomerId($customerId);
    $logger->debug('Loyalty Points Model: ', $loyaltyPointsModel->getData());
    return $loyaltyPointsModel;
}
}