<?php

namespace Custom\LoyalityPoints\Controller\Adminhtml\Rewards;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\Action;

class Grid extends Action
{
    protected $resultForwardFactory;

    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('index');
        return $resultForward;
    }
}