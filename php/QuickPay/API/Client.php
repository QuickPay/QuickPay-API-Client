<?php

namespace QuickPay\API;

require_once('RestClient.php');
require_once('Exception.php');

/**
 * Quickpay API Client
 *
 * @author Tim Warberg, QuickPay
 * @version 0.3
 */
class Client
{
    public static $baseUri = 'https://api.quickpay.net';
    public static $baseHeaders = array(
        'X-QuickPay-Client-API-Version: 1.0b',
        'Accept: application/json'
    );

    public static $netsFeeCardLocknames = array(
        "american-express",
        "american-express-dk",
        "dankort",
        "diners",
        "diners-dk",
        "edankort",
        "fbg1886",
        "jcb",
        "maestro",
        "maestro-dk",
        "mastercard",
        "mastercard-dk",
        "mastercard-debet-dk",
        "visa",
        "visa-dk",
        "visa-electron",
        "visa-electron-dk"
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
     * @return [array|StdObj] Without lockname: Array[ StdObj{lockname,amount,fee,total}, ...], with lockname: StdObj{lockname,amount,fee,total}
     */
    public function getNetsFee($amount, $lockname = null)
    {
        if(!is_int($amount)) {
            throw new Exception("Amount must be an integer");
        }
        $path = "/acquirers/nets/fees";
        if(is_string($lockname)) {
            if(!$this->isNetsCardWithFee($lockname)) {
                throw new Exception("Fees not available for Card '$lockname'");
            }
            $path .= "?lockname=$lockname";
        }
        $result = $this->_post($path, array('amount' => $amount));

        return $lockname === null ? $result : $result[0];
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

    public static function isNetsCardWithFee($lockname)
    {
        return in_array($lockname, self::$netsFeeCardLocknames);
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
    protected function _get($path, $params = array())
    {
        $uri    = self::getBaseUri() . $path;
        $client = RestClient::get($uri,$params,$this->_user,$this->_password,self::$baseHeaders);
        if($client->getResponseCode() !== 200) {
            throw new Exception(trim($client->getResponseMessage()) . ": " . trim($client->getResponse()));
        }
        if($client->getResponseContentType() != 'application/json') {
            throw new Exception("Unexpected response type: " . $client->getResponseContentType());
        }
        $result = json_decode($client->getResponse());

        if($result->success === false) {
            throw new Exception("Nets fees request failed: {$result->message}");
        }
        return $result->data;
    }

    /**
     * Perform REST POST request to API
     * @param string $path Absolute resource path
     * @param array $params Post params
     * @return array
     */
    protected function _post($path, $params)
    {
        $uri    = self::getBaseUri() . $path;
        $client = RestClient::post($uri, $params, $this->_user, $this->_password, "multipart/form-data", self::$baseHeaders);
        if($client->getResponseCode() !== 200) {
            throw new Exception(trim($client->getResponseMessage()) . ": " . trim($client->getResponse()));
        }
        if($client->getResponseContentType() != 'application/json') {
            throw new Exception("Unexpected response type: " . $client->getResponseContentType());
        }
        $result = json_decode($client->getResponse());

        if($result->success === false) {
            throw new Exception("Nets fees request failed: {$result->message}");
        }
        return $result->data;
    }
}
