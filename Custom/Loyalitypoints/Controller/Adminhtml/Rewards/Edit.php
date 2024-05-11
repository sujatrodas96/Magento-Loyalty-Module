<?php

namespace Custom\Loyalitypoints\Controller\Adminhtml\Rewards;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;

class Edit extends Action
{
    protected $resultForwardFactory;

    public function __construct(Action\Context $context, ForwardFactory $resultForwardFactory)
    {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('history'); // Forward to your custom grid controller
        return $resultForward;
    }
}
