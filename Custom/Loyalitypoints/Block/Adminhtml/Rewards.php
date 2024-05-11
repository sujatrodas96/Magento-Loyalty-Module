<?php
namespace Custom\Loyalitypoints\Block\Adminhtml;
 
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Custom\Loyalitypoints\Model\ResourceModel\LoyaltyPoints\CollectionFactory as LoyaltyPointsCollectionFactory;
use Psr\Log\LoggerInterface;
 
class Rewards extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'rewards.phtml';
 
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
 
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    protected $_customerRepositoryInterface;

    protected $_loyaltyPointsCollectionFactory;

    protected $_logger;
 
    /**
     * View constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param LoyaltyPointsCollectionFactory $loyaltyPointsCollectionFactory
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        ProductCollectionFactory $productCollectionFactory,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        LoyaltyPointsCollectionFactory $loyaltyPointsCollectionFactory,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_loyaltyPointsCollectionFactory = $loyaltyPointsCollectionFactory;
        $this->_logger = $logger;
        parent::__construct($context, $data);
    }
 
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(\Magento\Customer\Controller\RegistryConstants::CURRENT_CUSTOMER_ID);
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Reward Points');
    }
 
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Reward Points');
    }
 
    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
 

    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
 
    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
 
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }
 
    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

        public function getloyalitypoints()
    {
            $customerId = $this->getCustomerId();
            $loyaltyPoints = 0; // Default value

            if ($customerId) {
            try {
                // Load loyalty points based on customer_id from custom_loyalty_points table
                $loyaltyPointsCollection = $this->_loyaltyPointsCollectionFactory->create();
                $loyaltyPointsCollection->addFieldToFilter('customer_id', $customerId);

                if ($loyaltyPointsCollection->getSize() > 0) {
                    // Assuming that there's only one entry per customer, if not, you may need to adjust the logic
                    $loyaltyPointsItem = $loyaltyPointsCollection->getFirstItem();
                    $loyaltyPoints = $loyaltyPointsItem->getData('points');
                }
            } catch (\Exception $e) {
                // Handle exception
                $this->_logger->error($e->getMessage());
            }
        }

        return $loyaltyPoints;
    }

}
