<?php

namespace TMDB;

use TMDB\Data;
/**
 * Description of Response
 *
 * @author martin
 */
class Response
{
    protected $_data;
    
    public function __construct($response)
    {
        $this->_data = json_decode($response);
    }
    
    public function getData()
    {
        return new Data($this->_data);
    }
    
    public function hasData()
    {
        return $this->_data !== NULL;
    }
}
