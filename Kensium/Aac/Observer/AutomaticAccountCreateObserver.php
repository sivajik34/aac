<?php
/**
 * Copyright © 2015 Kensium Solutions. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kensium\Aac\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderCustomerManagementInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Psr\Log\LoggerInterface;


class AutomaticAccountCreateObserver implements ObserverInterface
{

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Api\OrderCustomerManagementInterface
     */
    protected $orderCustomerService;

    /** @var AccountManagementInterface */
    protected $accountManagement;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var $logger
     */
    private $logger;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param OrderCustomerManagementInterface $orderCustomerService
     * @param AccountManagementInterface $accountManagement
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        OrderCustomerManagementInterface $orderCustomerService,
        AccountManagementInterface $accountManagement,
        LoggerInterface $logger,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderCustomerService = $orderCustomerService;
        $this->accountManagement = $accountManagement;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getData('order_ids');
        if (isset($orderIds[0])) {
            $order = $this->orderRepository->get($orderIds[0]);
            if (!$order->getCustomerId()) {
                $isEmailAvailable = $this->accountManagement
                    ->isEmailAvailable($order->getCustomerEmail());
                //$this->logger->addInfo(print_r($isEmailAvailable,true));
                if ($isEmailAvailable) {
                    try {
                        $this->orderCustomerService->create($orderIds[0]);
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, $e->getMessage());
                        throw $e;
                    }
                    $this->coreRegistry->register('automatic_account', true);
                }

            }
        }

    }
}
