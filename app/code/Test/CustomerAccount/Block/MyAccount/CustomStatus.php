<?php
namespace Test\CustomerAccount\Block\MyAccount;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\AttributeRepository;

class CustomStatus extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * CustomStatus constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param AttributeRepository $attributeRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        AttributeRepository $attributeRepository,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $subscriberFactory, $customerRepository, $customerAccountManagement, $data);
        $this->attributeRepository = $attributeRepository;
    }

    public function getCustomStatusOptions()
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
        $attribute = $this->attributeRepository->get(\Magento\Customer\Model\Customer::ENTITY, 'custom_customer_status');
        return $attribute->getOptions();
    }

    public function getCurrentCustomStatus()
    {
        $customer = $this->customerSession->getCustomer();
        return $customer->getCustomCustomerStatus();
    }

}