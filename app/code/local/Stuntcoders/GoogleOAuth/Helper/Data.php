<?php

class Stuntcoders_GoogleOAuth_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param $email
     * @param $firstName
     * @param $lastName
     */
    public function authenticateAdmin($email, $firstName, $lastName)
    {
        if ($this->_adminExists($email)) {
            if ($this->_checkDomain($email)) {
                $this->_authAdmin($email);
            }
        } else {
            if ($this->_checkDomain($email)) {
                $this->_createAdmin($email, $firstName, $lastName);
                $this->_authAdmin($email);
            }
        }
    }

    /**
     * @param $email
     */
    private function _authAdmin($email)
    {
        Mage::getSingleton('core/session', array('name' => 'adminhtml'));

        $user = Mage::getModel('admin/user')->loadByUsername($email);

        if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
            Mage::getSingleton('adminhtml/url')->renewSecretUrls();
        }

        $session = Mage::getSingleton('admin/session');
        $session->setUser($user);
        $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
        Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));
    }

    /**
     * @param $email
     * @return bool
     */
    private function _checkDomain($email)
    {
        $check = false;
        $domainArray = Mage::helper('stuntcoders_googleoauth/google')->getDomainName();
        $domainName = substr(strrchr($email, '@'), 1);

        if (in_array($domainName, $domainArray)) {
            $check = true;
        }
        return $check;
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * @param $email
     * @return bool
     */
    private function _adminExists($email)
    {
        $check = false;
        $rolesUsers = Mage::getResourceModel('admin/roles_user_collection');

        foreach ($rolesUsers as $roleUser) {
            $user = Mage::getModel('admin/user')->load($roleUser->getUserId());
            array_push($result, $user->getEmail());

            if ($email === $user->getEmail()) {
                $check = true;
                break;
            }
        }
        return $check;
    }

    /**
     * @param $email
     * @param $firstName
     * @param $lastName
     */
    private function _createAdmin($email, $firstName, $lastName)
    {
        try {
            $user = Mage::getModel('admin/user')
                ->setData(array(
                    'username' => $email,
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                    'email' => $email,
                    'password' => Mage::helper('core')->getRandomString($length = 7),
                    'is_active' => 1
                ))->save();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }

        try {
            $user->setRoleIds(array(1))
                ->setRoleUserId($user->getUserId())
                ->saveRelations();
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
