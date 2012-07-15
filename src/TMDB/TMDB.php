<?php

namespace TMDB;

/**
 * Description of TMDB
 *
 * @author martin
 */
class TMDB
{
    protected $_apiKey;
    protected $_configuration = NULL;
    protected $_debug = false;
    public static $_baseUrl = 'http://api.themoviedb.org';
    public static $_apiVersion = 3;
    
    public function __construct($apiKey)
    {
        $this->_apiKey = $apiKey;
        $this->_configuration = $this->getConfiguration();
    }
    
    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;
    }
    
    public function setDebug($debug = true)
    {
        $this->_debug = $debug;
    }
    
    public function getConfiguration()
    {
        if ($this->_configuration === NULL) {
            $res = $this->request(array(
                'url' => self::buildUrl('configuration')
            ));
            
            return $res->getData();
        } else {
            return $this->_configuration;
        }
    }
    
    public static function buildUrl($method, array $params = array())
    {
        return sprintf(
            "%s/%s/%s%s",
            self::$_baseUrl,
            self::$_apiVersion,
            $method,
            (empty($params) ? '' : '/') . implode('/', $params)
        );
    }
    
    public function request(array $options)
    {
        if (!array_key_exists('url', $options)) {
            return NULL;
        } else {
            $url = $options['url'];
            unset($options['url']);
        }
        
        $options['api_key'] = $this->_apiKey;
        $query = http_build_query($options);
        $url = sprintf("%s?%s", $url, $query);
        
        if ($this->_debug) {
            var_dump('DEBUG: ' . $url);
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-type: application/json'
        )));
        
        $response = new Response(curl_exec($ch));
        
        if (!$response->hasData()) {
            echo "FAILED REQUEST: " . $url . "<br />";
            var_dump(curl_errno($ch));
            var_dump(curl_error($ch));
        }
        
        curl_close($ch);
        return $response;
    }
    
    protected function search(array $options)
    {
        
    }
    
    public static function camelCaseToUnderscore($str)
    {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }
    
    public static function underscoreToCamelCase($str, $capitalise_first_char = false)
    {
        if($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }
}