<?php

namespace Test\CustomerAccount\Plugin;

use Magento\Customer\CustomerData\Customer as CustomerData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Eav\Model\AttributeRepository;

/**
 * Class GetSectionData
 * @package Test\CustomerAccount\Plugin
 */
class GetSectionData
{
    const CUSTOM_STATUS_ATTRIBUTE_CODE = 'custom_customer_status';
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * GetSectionData constructor.
     *
     * @param CurrentCustomer $currentCustomer
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        AttributeRepository $attributeRepository
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Add customCustomerStatus to customer object.
     *
     * @param CustomerData $subject
     * @param array $data
     *
     * @return array
     */
    public function afterGetSectionData($subject, $data)
    {
        try {
            $customer = $this->currentCustomer->getCustomer();
            $customAttribute = $customer->getCustomAttribute('custom_customer_status')->getValue();
            $data['customCustomerStatus'] =  'status - ' . $this->getAttributeLabelByValue( $customAttribute);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {

        }


        return $data;
    }

    /**
     * Get attribute label by value.
     *
     * @param array $customAttribute
     *
     * @return string
     */
    protected function getAttributeLabelByValue($customAttribute)
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface $attribute */
        $attribute = $this->attributeRepository->get(\Magento\Customer\Model\Customer::ENTITY, self::CUSTOM_STATUS_ATTRIBUTE_CODE);
        foreach ($attribute->getOptions() as $option) {
            if ($option->getValue() == $customAttribute) {
                return $option->getLabel();
            }
        }

        return '';
    }
}