<?php
namespace Custom\Loyalitypoints\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Custom\Loyalitypoints\Model\LoyaltyPointsFactory;
use Magento\Customer\Model\CustomerFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    protected $loyaltyPointsFactory;
    protected $customerFactory;
    protected $logger;
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $storeManager;

    protected $sharedPointsFactory;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    LoyaltyPointsFactory $loyaltyPointsFactory,
    CustomerFactory $customerFactory,
    LoggerInterface $logger,
    TransportBuilder $transportBuilder,
    StateInterface $inlineTranslation,
    StoreManagerInterface $storeManager,
    \Custom\LoyalItyPoints\Model\SharedPointsFactory $sharedPointsFactory
    ) {
    parent::__construct($context);
    $this->loyaltyPointsFactory = $loyaltyPointsFactory;
    $this->customerFactory = $customerFactory;
    $this->logger = $logger;
    $this->transportBuilder = $transportBuilder;
    $this->inlineTranslation = $inlineTranslation;
    $this->storeManager = $storeManager;
    $this->sharedPointsFactory = $sharedPointsFactory;
    }
    
       //for updating loyalitypoints 
       public function updateLoyaltyPoints($customerId, $points)
    {
        $customer = $this->customerFactory->create()->load($customerId);
        $loyaltyPointsModel = $this->loyaltyPointsFactory->create();
        $loyaltyPointsModel->loadByCustomerId($customerId);

        if ($loyaltyPointsModel->getId()) {
            $currentPoints = $loyaltyPointsModel->getPoints();
            $loyaltyPointsModel->setPoints($currentPoints + $points);
        } else {
            $loyaltyPointsModel->setCustomerId($customerId);
            $loyaltyPointsModel->setPoints($points);
        }

        $loyaltyPointsModel->save();
    }

    //for getting our loyalitypoints 
    public function getLoyaltyPointsByCustomerId($customerId)
    {
        $loyaltyPointsModel = $this->loyaltyPointsFactory->create();
        $loyaltyPointsModel->loadByCustomerId($customerId);
        return $loyaltyPointsModel;
    }

    //after sending loyalitypoints to a customer increase the point to the customer automatically
    public function addLoyaltyPointsByEmail($recipientEmail, $points)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        $recipientCustomer = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($recipientEmail);

        if ($recipientCustomer->getId()) {
            $recipientCustomerId = $recipientCustomer->getId();
            $this->updateLoyaltyPoints($recipientCustomerId, $points);
        } else {
            // Log an error indicating that the recipient email does not belong to any customer
            $this->logger->error("Customer with email $recipientEmail not found.");
            throw new \Exception("No customer found with the provided email address.");
        }
    }

    //mail sending function
    public function sendPointsSharingEmail($senderCustomerId, $recipientEmail, $points)
{
    $senderCustomer = $this->customerFactory->create()->load($senderCustomerId);
    if ($senderCustomer->getId()) {
        $senderName = $senderCustomer->getName();

        $websiteId = $this->storeManager->getWebsite()->getId();
        $recipientCustomer = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($recipientEmail);
        if ($recipientCustomer->getId()) {
            $recipientName = $recipientCustomer->getName();

            if (!empty($senderName) && !empty($recipientName) && !empty($points)) {
                try {
                    $transport = $this->transportBuilder
                        ->setTemplateIdentifier('Custom_Loyalitypoints_email_template')
                        ->setTemplateOptions(['area' => 'frontend', 'store' => $this->storeManager->getStore()->getId()])
                        ->setTemplateVars([
                            'sender_name' => $senderName,
                            'recipient_name' => $recipientName,
                            'points' => $points
                        ])
                        ->setFrom(['email' => 'sales@example.com', 'name' => 'Sujatro Practice Store'])
                        ->addTo($recipientEmail)
                        ->getTransport();

                    $transport->sendMessage();

                    //sending data to table shared_points each share
                    $sharedPointsModel = $this->sharedPointsFactory->create();
                    $sharedPointsModel->setData([
                        'sender_customer_id' => $senderCustomerId,
                        'recipient_customer_id' => $recipientCustomer->getId(),
                        'points' => $points
                    ]);
                    $sharedPointsModel->save();
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            } else {
                $this->logger->error('One or more variables are empty for sending points sharing email.');
            }
        } else {
            $this->logger->error("Recipient customer with email $recipientEmail not found.");
        }
    } else {
        $this->logger->error("Sender customer with ID $senderCustomerId not found.");
    }
}

}