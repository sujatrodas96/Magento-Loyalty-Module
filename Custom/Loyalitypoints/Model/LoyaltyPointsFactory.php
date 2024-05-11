<?php
namespace Custom\Loyalitypoints\Model;

use Custom\Loyalitypoints\Model\LoyaltyPointsFactory as LoyaltyPointsFactory;
use Magento\Framework\ObjectManagerInterface;

class LoyaltyPointsFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new loyalty points model instance
     *
     * @param array $arguments
     * @return \Custom\Loyalitypoints\Model\LoyaltyPoints
     */
    public function create(array $arguments = [])
    {
        return $this->_objectManager->create(LoyalityPoints::class, $arguments);
    }
}