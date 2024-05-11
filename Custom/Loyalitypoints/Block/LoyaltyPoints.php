<?php
namespace Custom\Loyalitypoints\Block;

use Magento\Framework\View\Element\Template;
use Custom\Loyalitypoints\Helper\Data as LoyaltyPointsHelper;

class LoyaltyPoints extends Template
{
    protected $loyaltyPointsHelper;

    public function __construct(
        Template\Context $context,
        LoyaltyPointsHelper $loyaltyPointsHelper,
        array $data = []
    ) {
        $this->loyaltyPointsHelper = $loyaltyPointsHelper;
        parent::__construct($context, $data);
    }

    public function getLoyaltyPoints()
    {
        // Implement your logic to get loyalty points here
        // For example, you can retrieve it based on the current customer
        $customerId = $this->_customerSession->getCustomerId(); // Assuming customer is logged in
        return $this->loyaltyPointsHelper->getLoyaltyPointsByCustomerId($customerId);
    }
}
