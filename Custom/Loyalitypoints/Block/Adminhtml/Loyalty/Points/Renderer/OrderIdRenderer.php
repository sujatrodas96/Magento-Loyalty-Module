<?php
namespace Custom\Loyalitypoints\Block\Adminhtml\Loyalty\Points\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Backend\Block\Context;

class OrderIdRenderer extends AbstractRenderer
{
    protected $_orderItemCollectionFactory;

    public function __construct(
        Context $context,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        array $data = []
    ) {
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $orderItemCollection = $this->_orderItemCollectionFactory->create();
        $orderItemCollection->addFieldToFilter('order_id', $row->getEntityId());
        $orderItemCollection->addFieldToFilter('created_at', $row->getCreatedAt());

        if ($orderItemCollection->getSize() > 0) {
            $orderId = $orderItemCollection->getFirstItem()->getOrderId();
            return $orderId;
        }

        return '';
    }
}