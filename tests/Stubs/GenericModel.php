<?php
namespace CHMSTests\Common\Stubs;

class GenericModel implements \ArrayAccess 
{
    private $container = array();

    public function __construct($initial = []) {
        $this->container = $initial;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function __toArray()
    {
        return $this->container;
    }

    public function toArray()
    {
        return $this->container;
    }
}
