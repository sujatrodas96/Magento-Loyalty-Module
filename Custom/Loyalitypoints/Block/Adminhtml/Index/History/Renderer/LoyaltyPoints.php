<?php
namespace Custom\Loyalitypoints\Block\Adminhtml\Index\History\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class LoyaltyPoints extends AbstractRenderer
{
    /**
     * Render loyalty points
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $loyaltyPoints = $row->getData($this->getColumn()->getIndex());
        return $loyaltyPoints;
    }
}