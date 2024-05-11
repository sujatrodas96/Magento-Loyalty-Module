<?php
namespace Custom\Loyalitypoints\Block\Adminhtml\Loyalty\Points;

use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Custom\Loyalitypoints\Model\ResourceModel\LoyaltyPoints\CollectionFactory;

class Grid extends Extended
{
    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('loyaltyPointsGrid');
        $this->setDefaultSort('customer_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        
        // Join the customer table
        $collection->getSelect()->join(
            ['customer' => $collection->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            ['customer_name' => 'customer.firstname', 'customer_email' => 'customer.email']
        );
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'customer_id',
            [
                'header' => __('Customer ID'),
                'index' => 'customer_id',
                'filter_index' => 'main_table.customer_id',
            ]
        );

        $this->addColumn(
            'customer_name',
            [
                'header' => __('Customer Name'),
                'index' => 'customer_name',
                'filter_index' => 'customer.firstname',
            ]
        );

        $this->addColumn(
            'customer_email',
            [
                'header' => __('Customer Email'),
                'index' => 'customer_email',
                'filter_index' => 'customer.email',
            ]
        );

        $this->addColumn(
            'points',
            [
                'header' => __('Points'),
                'index' => 'points',
                'filter_index' => 'main_table.points',
            ]
        );

        // Add Edit button column
        $this->addColumn(
            'edit',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getCustomerId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'customer/rewards/edit',
                            'params' => ['id' => 'customer_id']
                        ],
                        'field' => 'customer_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            'customer/rewards/edit',
            ['id' => $row->getCustomerId()] // Change 'customer_id' to 'id'
        );
    }
}
