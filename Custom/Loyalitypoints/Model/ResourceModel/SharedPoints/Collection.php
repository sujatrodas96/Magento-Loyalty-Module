<?php
namespace Custom\LoyalityPoints\Model\ResourceModel\SharedPoints;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Custom\LoyalityPoints\Model\SharedPoints as SharedPointsModel;
use Custom\LoyalityPoints\Model\ResourceModel\SharedPoints as SharedPointsResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'shared_points_id';

    protected function _construct()
    {
        $this->_init(
            SharedPointsModel::class,
            SharedPointsResourceModel::class
        );
    }
}
