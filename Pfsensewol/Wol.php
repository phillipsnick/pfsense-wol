<?php
/**
 * pfSense WOL 
 * 
 * @author      Nick Phillips (nick@linkstudios.co.uk)
 * @copyright   Copyright 2013, Nick Phillips (nick@linkstudios.co.uk)
 * @license     MIT Licence
 */

namespace Pfsensewol;

class Wol 
{
    /**
     * @var string  Connect to pfSense using HTTPS (default true)
     */
    private $https = true;
    
    /**
     * @var string  pfSense hostname or IP address
     */
    private $pfsense = '';
    
    /**
     * @var string  pfSense Username
     */
    private $username = '';
    
    /**
     * @var string  pfSense Password
     */
    private $password = '';
    
    
    /**
     * @param   array   $params
     * @throws  Exception
     */
    public function __construct($params = array())
    {
        if (!function_exists('curl_exec')) {
            throw new \Exception('pfSense WOL requires the PHP CURL extension.');
        }
    
        foreach($params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    
    /**
     * Set if we should connect with HTTPS or HTTP
     * 
     * @param   bool $flag
     * @return  \Pfsensewol\Wol
     */
    public function setHttps($flag)
    {
        $this->https = (bool) $flag;
        return $this;
    }
    
    /**
     * Set the hostname or IP address of pfSense
     * 
     * @param   string  $host
     * @return  \Pfsensewol\Wol
     */
    public function setPfsense($host)
    {
        $this->pfsense = $host;
        return $this;
    }
    
    /**
     * Set the username to login to pfSense with
     * 
     * @param   string  $username
     * @return  \Pfsensewol\Wol
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    
    /**
     * Set the password to login to pfSense with
     * 
     * @param   string  $password
     * @return  \Pfsensewol\Wol
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
        
    /**
     * Send the request to wake a specific MAC address, you must define the interface to use on pfSense
     *  eg. lan, opt1, opt2 etc
     * 
     * @param   string  $mac
     * @param   string  $interface
     * @return  bool
     * @throws  Exception
     */
    public function send($mac, $interface)
    {
        if (empty($this->pfsense) || empty($this->username)) {
            throw new \Exception('You must provide the pfSense host/IP and a username.');
        }
        
        if ($this->https) {
            $url = 'https://';
        } else {
            $url = 'http://';
        }
        
        $url.= $this->pfsense;
        $cookieFileName = tempnam(sys_get_temp_dir(), 'wolcookie');
       
        $curlOptions = array(
            CURLOPT_RETURNTRANSFER =>   true,
            CURLOPT_SSL_VERIFYHOST =>   2,
            CURLOPT_SSL_VERIFYPEER =>   false,
            CURLOPT_FOLLOWLOCATION =>   true,
            CURLOPT_COOKIEFILE =>       $cookieFileName,
            CURLOPT_COOKIEJAR =>        $cookieFileName,
            CURLOPT_HEADER =>           0,
        );
        
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        curl_setopt($ch, CURLOPT_URL, $url);
        
        // first we need to get the session ID for the CSRF checks
        $loginPage = curl_exec($ch);
        
        if ($loginPage === false) {
            throw new \Exception('Unable to connect to pfSense');
        }
        
        preg_match('/value=\"sid:([^}]*)"/Uis', $loginPage, $matches);
        
        if (empty($matches[1])) {
            curl_close($ch);
            throw new \Exception('Unable to find session ID from pfSense');
        }
        
        $loginString = 'login=Login&__csrf_magic=' . urlencode('sid:' . $matches[1]) . '&usernamefld=' . $this->username . '&passwordfld=' . $this->password;
        
        // login to pfSense
        curl_setopt($ch, CURLOPT_URL, $url . '/index.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $loginString);
       
        // we ignore this result as we need to now make the WOL request
        $result = curl_exec($ch);
        
        curl_setopt($ch, CURLOPT_URL, $url . '/services_wol.php?mac=' . urlencode($mac) . '&if=' . $interface);
        curl_setopt($ch, CURLOPT_PORT, 0);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return stristr($result, 'Sent magic packet to');
    }
}