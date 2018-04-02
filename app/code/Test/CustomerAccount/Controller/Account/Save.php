<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Test\CustomerAccount\Controller\Account;

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Validator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->session = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * Change custom customer status.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $request = $this->getRequest();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($customerAccountEdit = $request->getParam('customer_account_edit')) {
            $validFormKey = $this->formKeyValidator->validate($this->getRequest());

            if ($validFormKey && $this->getRequest()->isPost()) {
                $customer = $this->getCustomerDataObject($this->session->getCustomerId());
                $customer->setCustomAttribute('custom_customer_status', $customerAccountEdit);
                try {
                    $this->customerRepository->save($customer);
                    $this->messageManager->addSuccess(__('You saved the account information.'));
                    return $resultRedirect->setPath('customerCustomAttribute/account/form');
                } catch (InvalidEmailOrPasswordException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (UserLockedException $e) {
                    $message = __(
                        'You did not sign in correctly or your account is temporarily disabled.'
                    );
                    $this->session->logout();
                    $this->session->start();
                    $this->messageManager->addError($message);
                    return $resultRedirect->setPath('customer/account/login');
                } catch (InputException $e) {
                    $this->messageManager->addError($e->getMessage());
                    foreach ($e->getErrors() as $error) {
                        $this->messageManager->addError($error->getMessage());
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('We can\'t save the customer.'));
                }

                $this->session->setCustomerFormData($this->getRequest()->getPostValue());
            }
        }

        $resultRedirect->setRefererOrBaseUrl();
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        return $resultRedirect->setPath('*/*/*');
    }

    /**
     * Get customer data object
     *
     * @param int $customerId
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    private function getCustomerDataObject($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }
}
