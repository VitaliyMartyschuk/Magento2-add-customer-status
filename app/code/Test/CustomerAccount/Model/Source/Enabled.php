<?php
namespace Test\CustomerAccount\Model\Source;

/**
 * Class Enabled
 * @package Test\CustomerAccount\Model\Source
 */
class Enabled extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach ($this->getValues() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }

    /**
     * Get option text
     *
     * @param int|string $value
     * @return null|string
     */
    public function getOptionText($value)
    {
        $options = $this->getValues();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return null;
    }

    /**
     * Get values
     *
     * @return array
     */
    protected function getValues()
    {
        return [
            1 => __('Enabled'),
            2 => __('Disabled')
        ];
    }
}
