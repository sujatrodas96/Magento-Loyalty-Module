<?php
namespace Custom\Loyalitypoints\Controller\Adminhtml\Index;


use Magento\Backend\App\Action;

class History extends Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
