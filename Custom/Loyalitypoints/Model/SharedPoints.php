<?php
namespace Custom\LoyalityPoints\Model;

use Magento\Framework\Model\AbstractModel;

class SharedPoints extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Custom\LoyalityPoints\Model\ResourceModel\SharedPoints::class);
    }
}
