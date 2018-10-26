<?php

class Stuntcoders_GoogleOAuth_Adminhtml_CallbackController extends Mage_Adminhtml_Controller_Action
{
    private function login($requiredFields)
    {
        $oAuthHelper = Mage::helper('stuntcoders_googleoauth/google');
        $code = $this->getRequest()->getParam('code');

        if (!$code) {
            $this->_redirect('/');
            return;
        }

        $accessToken = $oAuthHelper->getAccessToken($code);
        $userInfo = $oAuthHelper->getUserInfo($accessToken['access_token']);

        Mage::helper('stuntcoders_googleoauth')->authenticateAdmin(
            $userInfo['result'][$requiredFields[0]],
            $userInfo['result'][$requiredFields[1]],
            $userInfo['result'][$requiredFields[2]]
        );
    }

    public function googleAction()
    {
        $this->login(array('email', 'given_name', 'family_name'));
        $this->_redirect('/');
    }

    /**
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->getRequest()->setDispatched(true);
        $this->getRequest()->setInternallyForwarded(true);

        return parent::preDispatch();
    }

    protected function _isAllowed()
    {
        return true;
    }
}
