<?php
namespace Custom\Loyalitypoints\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class LoyalityPoints extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('custom_loyalty_points', 'loyalty_points_id');
    }

        public function loadByCustomerId($object, $customerId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('customer_id = ?', $customerId);

        $data = $connection->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }

        return $this;
    }
}