<?php

namespace QuickPay\API;

require_once('RestClient.php');
require_once('Exception.php');

/**
 * Quickpay API Client
 *
 * @author Tim Warberg, QuickPay
 * @version 0.1
 */
class Client
{
    public static $baseUri = 'https://api.quickpay.net';
    public static $baseHeaders = array(
        'X-QuickPay-Client-API-Version: 0.1'
    );
    
    /**
     * @var string
     */
    protected $_user;
    
    /**
     * @var string
     */
    protected $_password;
    
    /**
     * @param string $user
     * @param string $password
     */
    public function __construct($user, $password)
    {
        $this->_user     = $user;
        $this->_password = $password;
    }
    
    /**
     * Get fee(s) for Nets payment method(s).
     * fee for all available creditcard types is returned.
     * @param integer $amount
     * @param string $lockname
     * @return array Without lockname: Array[ Array['lockname','amount','fee','total'], ...], with lockname: Array['lockname','amount','fee','total']
     */
    public function getNetsFee($amount, $lockname = null)
    {
        if(!is_int($amount)) {
            throw new Exception("Amount must be an integer");
        }
        $path = "/acquirers/nets/fees/$amount";
        if(is_string($lockname)) {
            $path .= "/$lockname";
        }
        return $this->_get($path);
    }
    
    /**
     * Get last recorded status of Nets acquirer.
     * @return array Array['status','updated_at']
     */
    public function getNetsStatus()
    {
        $path = "/acquirers/nets/status";
        return $this->_get($path);
    }
    
    /**
     * Get API base URI
     * @return string
     */
    public static function getBaseUri()
    {
        return self::$baseUri;
    }
    
    /**
     * Set API base URI
     * @param string $uri
     */
    public static function setBaseUri($uri)
    {
        self::$baseUri = $uri;
    }
    
    /**
     * Perform REST GET request to API
     * @param string $path Absolute resource path
     * @return array
     */
    protected function _get($path)
    {
        $uri    = self::getBaseUri() . $path;
        $client = RestClient::get($uri,$params,$this->_user,$this->_password,self::$baseHeaders);
        if($client->getResponseCode() !== 200) {
            throw new Exception(trim($client->getResponseMessage()) . ": " . trim($client->getResponse()));
        }
        if($client->getResponseContentType() != 'application/json') {
            throw new Exception("Unexpected response type: " . $client->getResponseContentType());
        }
        return json_decode($client->getResponse());
    }
}
