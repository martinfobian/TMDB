<?php

namespace TMDB\Asset;

/**
 * Description of Movie
 *
 * @author martin
 */
class Movie
{
    protected $_data;
    
    public function __construct(\TMDB\Data $data)
    {
        $this->_data = $data;
    }
    
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->_data, $name), $arguments);
    }
}