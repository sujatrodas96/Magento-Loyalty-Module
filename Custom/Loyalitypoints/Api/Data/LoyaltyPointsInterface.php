<?php
namespace Custom\Loyalitypoints\Api\Data;

interface LoyaltyPointsInterface
{
    const LOYALTY_POINTS_ID = 'loyalty_points_id';
    const CUSTOMER_ID = 'customer_id';
    const POINTS = 'points';

    public function getLoyaltyPointsId();
    public function setLoyaltyPointsId($loyaltyPointsId);
    public function getCustomerId();
    public function setCustomerId($customerId);
    public function getPoints();
    public function setPoints($points);
}