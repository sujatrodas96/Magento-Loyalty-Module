<?php

namespace Custom\Loyalitypoints\Block\Order\View;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getData('order');
    }
}