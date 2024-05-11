<?php
namespace Custom\Loyalitypoints\Block\Adminhtml\Loyalty\Points;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Sales\Model\ResourceModel\Order\Grid\CollectionFactory as OrderGridCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;

class EditGrid extends Extended
{
    protected $_orderGridCollectionFactory;
    protected $_orderItemCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        OrderGridCollectionFactory $orderGridCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        array $data = []
    ) {
        $this->_orderGridCollectionFactory = $orderGridCollectionFactory;
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('loyaltyPointsGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        $orderGridCollection = $this->_orderGridCollectionFactory->create();
        $orderGridCollection->addFieldToFilter('customer_id', $customerId);
        $orderGridCollection->addFieldToFilter('loyalty_points', ['notnull' => true]);

        $orderItemCollection = $this->_orderItemCollectionFactory->create();
        $orderItemCollection->addFieldToFilter('order_id', ['in' => $orderGridCollection->getColumnValues('entity_id')]);

        $this->setCollection($orderGridCollection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'created_at',
            [
                'header' => __('Order Date'),
                'index' => 'created_at',
                'type' => 'datetime',
            ]
        );

        $this->addColumn(
            'order_id',
            [
                'header' => __('Order ID'),
                'index' => 'order_id',
                'renderer' => 'Custom\Loyalitypoints\Block\Adminhtml\Loyalty\Points\Renderer\OrderIdRenderer',
            ]
        );

        $this->addColumn(
            'loyalty_points',
            [
                'header' => __('Loyalty Points'),
                'index' => 'loyalty_points',
            ]
        );

        

        return parent::_prepareColumns();
    }
}