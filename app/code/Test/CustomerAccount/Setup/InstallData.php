<?php

namespace Test\CustomerAccount\Setup;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Customer\Model\Customer;

/**
 * Class InstallData
 *
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    protected $attributeSetFactory;
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param SetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        SetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var Set $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $customStatusAttribute = ['code' => 'custom_customer_status', 'label' => 'Custom customer status'];
            $customerSetup->addAttribute(
                Customer::ENTITY,
                $customStatusAttribute['code'],
                [
                    'type' => 'int',
                    'label' => $customStatusAttribute['label'],
                    'source' => 'Test\CustomerAccount\Model\Source\Enabled',
                    'input' => 'select',
                    'required' => false,
                    'visible' => true,
                    'visible_on_front' => true,
                    'user_defined' => true,
                    'sort_order' => 200,
                    'position' => 200,
                    'system' => 0
                ]
            );

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $customStatusAttribute['code'])
                ->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => ['adminhtml_customer', 'customer_account_edit']
                ]);

            $attribute->getResource()->save($attribute);

        $setup->endSetup();
    }
}
