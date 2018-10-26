<?php

require_once(Mage::getBaseDir('lib') . '/OAuth2/Client.php');
require_once(Mage::getBaseDir('lib') . '/OAuth2/GrantType/IGrantType.php');
require_once(Mage::getBaseDir('lib') . '/OAuth2/GrantType/AuthorizationCode.php');

class Stuntcoders_GoogleOAuth_Helper_Google extends Mage_Core_Helper_Abstract
{
    const AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
    const TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
    const USER_URL = 'https://www.googleapis.com/oauth2/v2/userinfo';

    const SYSTEM_CONFIG_GOOGLE_APPLICATION_ID = 'stuntcoders_googleoauth/google/application_id';
    const SYSTEM_CONFIG_GOOGLE_SHARED_SECRET = 'stuntcoders_googleoauth/google/shared_secret';
    const SYSTEM_CONFIG_GOOGLE_ENABLE = 'stuntcoders_googleoauth/google/enable';
    const SYSTEM_CONFIG_GOOGLE_DOMAIN_NAME = 'stuntcoders_googleoauth/google/domain_name';

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->_getOAuthClient()->getAuthenticationUrl(
            self::AUTH_URL,
            $this->getCallbackUrl(),
            array('scope' => 'email profile')
        );
    }

    /**
     * @param $code
     * @return mixed
     * @throws \OAuth2\Exception
     */
    public function getAccessToken($code)
    {
        $client = $this->_getOAuthClient();
        $response = $client->getAccessToken(self::TOKEN_URL, 'authorization_code', array (
            'code' => $code,
            'redirect_uri' => $this->getCallbackUrl()
        ));

        return $response ['result'];
    }

    /**
     * @param $accessToken
     * @return array
     * @throws \OAuth2\Exception
     */
    public function getUserInfo($accessToken)
    {
        $client = $this->_getOAuthClient();
        $client->setAccessToken($accessToken);

        return $client->fetch(self::USER_URL);
    }

    /**
     * @return string
     */
    public function getApplicationId()
    {
        return Mage::getStoreConfig(self::SYSTEM_CONFIG_GOOGLE_APPLICATION_ID);
    }

    /**
     * @return string
     */
    public function getSharedSecret()
    {
        return Mage::getStoreConfig(self::SYSTEM_CONFIG_GOOGLE_SHARED_SECRET);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::SYSTEM_CONFIG_GOOGLE_ENABLE);
    }

    /**
     * @return array
     */
    public function getDomainName()
    {
        $result = array();
        $domains = Mage::getStoreConfig(self::SYSTEM_CONFIG_GOOGLE_DOMAIN_NAME);
        if ($domains) {
            $domains = unserialize($domains);
            if (is_array($domains)) {
                foreach($domains as $domainsRow) {
                    $domain_name = $domainsRow['domain_name'];
                    array_push($result, $domain_name);
                }
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return Mage::getUrl('adminhtml/callback/google');
    }

    /**
     * @return \OAuth2\Client
     * @throws \OAuth2\Exception
     */
    protected function _getOAuthClient()
    {
        return new OAuth2\Client($this->getApplicationId(), $this->getSharedSecret());
    }
}
