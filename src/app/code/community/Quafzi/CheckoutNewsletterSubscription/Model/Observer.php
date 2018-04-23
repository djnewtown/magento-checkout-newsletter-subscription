<?php
class Quafzi_CheckoutNewsletterSubscription_Model_Observer
{

    public function addCheckbox ($observer)
    {
        if ($observer->getBlock() instanceof Mage_Checkout_Block_Agreements
            && false === (boolean)(int)Mage::getStoreConfig('advanced/modules_disable_output/Quafzi_CheckoutNewsletterSubscription')
        ) {
            if(Mage::getSingleton('customer/session')->isLoggedIn()){
                $email = Mage::getSingleton('customer/session')->getCustomer()->getData('email');
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);

                if($subscriber->getId())
                {
                    $isActive = $subscriber->getData('subscriber_status') == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;

                    if ($isActive)
                    {
                        $show = false;
                    }
                    else
                    {
                        $show = true;
                    }
                }
                else
                {
                    $show = true;
                }
            }
            else
            {
                $show = true;
            }

            if ($show)
            {

                $textDataProtection = Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('dataProtectionTextCheckout')->toHtml();
//                if ($varDataProtectionCheckout)
//                {
//                    $textDataProtection = $varDataProtectionCheckout->getValue('html');
//                }
//                else
//                {
//                    $textDataProtection = 'Lorem Ipsum ... dataProtectionTextCheckout not found! ';
//
//                }

                $html = $observer->getTransport()->getHtml();
                $checkboxHtml = '<li><p class="agree">'
                    . '<input id="subscribe_newsletter" name="is_subscribed" checked="checked" value="1" class="checkbox" type="checkbox" />'
                    . '<label for="subscribe_newsletter">' . Mage::helper('sales')->__('Subscribe to Newsletter') . '</label>'
                    . '</p><div id="checkout-dataprotection-textbox">'.$textDataProtection.'</div></li>';
                $html = str_replace('</ol>', $checkboxHtml . '</ol>', $html);
                $observer->getTransport()->setHtml($html);
            }

        }
    }

    public function subscribe ($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getBillingAddress() && Mage::app()->getRequest()->getParam('is_subscribed', false)) {
            $status = Mage::getModel('newsletter/subscriber')
                ->setImportMode(true)
                ->subscribe($quote->getBillingAddress()->getEmail());
        }
    }

}
