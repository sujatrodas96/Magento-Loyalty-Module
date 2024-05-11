<?php
namespace Custom\Loyalitypoints\Model\ResourceModel\LoyaltyPoints;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'loyalty_points_id';

    protected function _construct()
    {
        $this->_init(
            \Custom\Loyalitypoints\Model\LoyalityPoints::class,
            \Custom\Loyalitypoints\Model\ResourceModel\LoyalityPoints::class
        );
    }

    
}