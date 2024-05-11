<?php
namespace Custom\LoyalityPoints\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SharedPoints extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('shared_points', 'shared_points_id');
    }
}
