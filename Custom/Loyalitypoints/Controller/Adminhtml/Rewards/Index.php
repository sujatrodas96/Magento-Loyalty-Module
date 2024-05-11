<?php
namespace Custom\Loyalitypoints\Controller\Adminhtml\Rewards;

use Magento\Backend\App\Action;

class Index extends Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
