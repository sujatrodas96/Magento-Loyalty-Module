<?php
namespace Custom\Loyalitypoints\Observer;

use Magento\Framework\Event\ObserverInterface;
use Custom\Loyalitypoints\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\ResourceModel\GridPool;

class AwardLoyaltyPoints implements ObserverInterface
{
    protected $loyaltyPointsHelper;
    protected $orderRepository;
    protected $orderResource;
    protected $gridPool;

    public function __construct(
        Data $loyaltyPointsHelper,
        OrderRepositoryInterface $orderRepository,
        OrderResource $orderResource,
        GridPool $gridPool
    ) {
        $this->loyaltyPointsHelper = $loyaltyPointsHelper;
        $this->orderRepository = $orderRepository;
        $this->orderResource = $orderResource;
        $this->gridPool = $gridPool;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $grandTotal = $order->getGrandTotal();

        if ($grandTotal >= 50) {
            $loyaltyPoints = ($grandTotal * 0.1);
            $this->loyaltyPointsHelper->updateLoyaltyPoints($customerId, $loyaltyPoints);

            $order->setData('loyalty_points', $loyaltyPoints);
            $this->orderRepository->save($order);

            $this->orderResource->getConnection()->update(
                $this->orderResource->getTable('sales_order_grid'),
                ['loyalty_points' => $loyaltyPoints],
                ['entity_id = ?' => $order->getId()]
            );

            $this->gridPool->refreshByOrderId($order->getId());
        }
    }
}