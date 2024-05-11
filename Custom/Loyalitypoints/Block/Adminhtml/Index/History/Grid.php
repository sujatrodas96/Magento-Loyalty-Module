<?php
namespace Custom\Loyalitypoints\Block\Adminhtml\Index\History;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Custom\Loyalitypoints\Model\ResourceModel\LoyaltyPoints\CollectionFactory as LoyaltyPointsCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class Grid extends WidgetGrid
{
    protected $_loyaltyPointsCollectionFactory;
    protected $_orderCollectionFactory;

    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        LoyaltyPointsCollectionFactory $loyaltyPointsCollectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        $this->_loyaltyPointsCollectionFactory = $loyaltyPointsCollectionFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('id');

        // Prepare loyalty points collection
        $loyaltyPointsCollection = $this->_loyaltyPointsCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId);

        // Join with sales_order table to get created_at
        $loyaltyPointsCollection->getSelect()->joinLeft(
            ['order' => $loyaltyPointsCollection->getTable('sales_order')],
            'main_table.order_id = order.entity_id',
            ['order_created_at' => 'order.created_at']
        );

        $this->setCollection($loyaltyPointsCollection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'order_date',
            [
                'header' => __('Order Date'),
                'index' => 'order_created_at', // Use the joined field
                'type' => 'datetime',
                'filter_index' => 'order.created_at' // Use the original field for filtering
            ]
        );

        $this->addColumn(
            'loyalty_points',
            [
                'header' => __('Loyalty Points'),
                'index' => 'loyalty_points',
                'filter_index' => 'loyalty_points'
            ]
        );

        parent::_prepareColumns();
        return $this;
    }
}
