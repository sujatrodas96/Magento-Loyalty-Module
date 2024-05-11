<?php
namespace Custom\Loyalitypoints\Block\Adminhtml\Index\History\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

class DateTime extends AbstractRenderer
{
    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * @param Context $context
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param array $data
     */
    public function __construct(
        Context $context,
        DateTimeFormatterInterface $dateTimeFormatter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * Render order date
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $date = $row->getData($this->getColumn()->getIndex());
        return $this->dateTimeFormatter->formatDate($date, \IntlDateFormatter::MEDIUM);
    }
}