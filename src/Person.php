<?php
namespace DapperDan;

class Person {

    public function __construct($array) {
        foreach($array as $key => &$val ) {
            if ( is_string($key) ) {
                if ( preg_match("/count/i", $key) ) { continue; }
                if ( is_array($val) && array_key_exists("count", $val) ) {
                    unset($val['count']);
                }

                if ( is_array($val) && count($val) == 1 ) {
                    $val = $val[0];
                }

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

    public function get($key) {
        if ( is_array($key) ) {
            $ret = array();
            foreach($key as $k) {
                $ret[$k] = $this->get($k);
            }
            return $ret;
        }

        if ( $this->has($key) ) {
            return $this->$key; 
        }
    }

    /**
     *  object wrapper for property_exists
     *
     *  @param string
     *  @return boolean
     *
     */

    public function has($key) { return property_exists($this, $key); }
}