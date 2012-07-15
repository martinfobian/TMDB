<?php

namespace TMDB;

use TMDB\TMDB;

/**
 * Description of Data
 *
 * @author martin
 */
class Data implements \Iterator, \ArrayAccess, \Countable
{
    const TYPE_CLASS = 'class';
    const TYPE_ARRAY = 'array';
    const TYPE_ATOMIC = 'atomic';
    
    private $_position = 0;
    protected $_data;
    protected $_type;
    
    public function __construct($data)
    {
        $this->_data = $data;
        $this->_type = $this->getType($data);
        
        $this->_position = 0;
    }
    
    public function getType($object)
    {
        if (is_array($object)) {
            return self::TYPE_ARRAY;
        } elseif (is_object ($object)) {
            return self::TYPE_CLASS;
        } else {
            return self::TYPE_ATOMIC;
        }
    }
    
    public function isEmpty()
    {
        return empty($this->_data);
    }
    
    public function isCompositeType($object)
    {
        return $this->getType($object) !== self::TYPE_ATOMIC;
    }
    
    public function returnProperty($propertyName)
    {
        if ($this->_type == self::TYPE_ARRAY) {
            $propertyExists = array_key_exists($propertyName, $this->_data);
        } else {
            $propertyExists = property_exists($this->_data, $propertyName);
        }
            
        if ($propertyExists) {

            $property = $this->_type == self::TYPE_ARRAY
                ? $this->_data[$propertyName]
                : $this->_data->$propertyName;

            if (is_array($property) || $property instanceof \stdClass) {
                return new \TMDB\Data($property);
            } else {
                return $property;
            }
        }
    }
    
    public function __call($name, $arguments)
    {
        $matches = array();
        if (preg_match('/get(.*)/', $name, $matches)) {
            
            $propertyName = \TMDB\TMDB::camelCaseToUnderscore($matches[1]);
            return $this->returnProperty($propertyName);
        }
    }

    public function rewind() {
        $this->_position = 0;
    }

    public function current() {
        return new \TMDB\Data($this->_data[$this->_position]);
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        ++$this->_position;
    }

    public function valid() {
        return $this->_type == self::TYPE_ARRAY && isset($this->_data[$this->_position]);
    }
    
    
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return $this->_data !== NULL && isset($this->_data[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->_data[$offset]);
    }
    
    public function offsetGet($offset) {
        
//        var_dump($this->_data[$offset]);
//        var_dump($this->isCompositeType($this->_data[$offset]));
//        var_dump($this->getType($data));
//        var_dump(get_class($data));
//        var_dump(is_object($data));
//        die();
        
        if (!isset($this->_data[$offset])) {
            
            return NULL;
            
        } elseif ($this->isCompositeType($this->_data[$offset])) {
            
            return new \TMDB\Data($this->_data[$offset]);
            
        } else {
            
            return $this->_data[$offset];
            
        }
    }
    
    public function count()
    {
        return count($this->_data);
    }
}