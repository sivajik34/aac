<?php
/**
* Copyright © 2015 Kensium Solutions. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Kensium\Aac\Block\Onepage;

class NewAccountInfo extends \Magento\Framework\View\Element\Template
{

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\Registry $coreRegistry,
    \Magento\Checkout\Model\Session $checkoutSession,
    array $data = []
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $isAutomaticAccount= $this->coreRegistry->registry('automatic_account');
        if (!$isAutomaticAccount) {
            return '';
        }
        return parent::toHtml();
    }

    /**
     * Retrieve current email address
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getEmailAddress()
    {
        return $this->checkoutSession->getLastRealOrder()->getCustomerEmail();
    }

}
