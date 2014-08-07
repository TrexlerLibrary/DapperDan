<?php
namespace DapperDan;

class Person {

    public function __construct($array) {
        foreach( $array as $key => $val ) {
            if ( is_string($key) ) {
                $this->$key = $val;
            }
        }
    }
    
    /**
     *  retrieves a value for key if exists
     *
     *  @param  string|array    can take either a string key or an array of keys
     *  @return string|array    depending on input returns a single value or an associative array of values
     *
     */

    public function get_value($key) {
        if ( is_array($key) ) {
            $ret = array();
            foreach($key as $k) {
                $ret[$k] = $this->get_value($k);
            }
            return $ret;
        }

        if ( $this->has_key($key) ) {
            return $this->$key; 
        }
    }

    /**
     *  alias for Person::get_value()
     *
     *  @param  string|array
     *  @return string|array
     *
     */

    public function get_values($key) { return $this->get_value($key); }

    /**
     *  object wrapper for property_exists
     *
     *  @param string
     *  @return boolean
     *
     */

    public function has_key($key) { return property_exists($this, $key); }
}