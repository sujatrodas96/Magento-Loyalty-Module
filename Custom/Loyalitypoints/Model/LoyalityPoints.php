<?php
namespace Custom\Loyalitypoints\Model;

use Custom\Loyalitypoints\Api\Data\LoyaltyPointsInterface;
use Magento\Framework\Model\AbstractModel;

class LoyalityPoints extends AbstractModel implements LoyaltyPointsInterface
{
    protected function _construct()
    {
        $this->_init(\Custom\Loyalitypoints\Model\ResourceModel\LoyalityPoints::class);
    }

    public function getLoyaltyPointsId()
    {
        return $this->getData(self::LOYALTY_POINTS_ID);
    }

    public function setLoyaltyPointsId($loyaltyPointsId)
    {
        return $this->setData(self::LOYALTY_POINTS_ID, $loyaltyPointsId);
    }

    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    public function getPoints()
    {
        return $this->getData(self::POINTS);
    }

    public function setPoints($points)
    {
        return $this->setData(self::POINTS, $points);
    }

    public function loadByCustomerId($customerId)
    {
        $this->getResource()->loadByCustomerId($this, $customerId);
        return $this;
    }

    
}