<?php

namespace DapperDan;

class Server {

    public $url;
    public $port;

    private $username = null;
    private $password = null;

    public function __construct($url, $port = 389) {
        $this->url = $url;
        $this->port = $port;
    }

    /**
     *  preps array|string into an LDAP-ready query
     *  
     *  array is structured as such:
     *      ldap_key => value
     *  array _must_ contain an "operator" key/val pair. as of now,
     *  the only values acceptable are "&" or "|"
     *  
     *  @param  array|string
     *  @return string
     *
     */

    public function prep_query($terms) {
        if ( is_array($terms) ) {
            $query = "";
            $operator = $terms['operator'];
            unset($terms['operator']);

            foreach($terms as $key => $val) {
                // multiples w/ same key (for OR statements)
                if ( is_array($val) ) {
                    foreach($val as $v) {
                        $query .= "(" . $key . "=" . $v . ")";
                    }
                } else {
                    $query .= "(" . $key . "=" . $val . ")";
                }
            }

            if ( count($terms) !== 1 || isset($operator) ) {
                $query = "(" . $operator . $query . ")";
            } 
        } else {
            $best_case_single_reg   = "/^\(\w+\=[\w\,\_\-\=]+\)$/";
            $best_case_multiple_reg = "/^\([&|](?:(\(\w+\=[\w\,\_\-\=]+\))+)\)$/";
            $stripped_reg           = "/^\w+\=[\w\,\_\-\=]+$/";
            // well formed! easy peasy
            if ( preg_match($best_case_single_reg, $terms) || preg_match($best_case_multiple_reg, $terms) ) {
                $query = $terms;
            } elseif ( preg_match($stripped_reg, $terms) ) {
                $query = "(" . $terms . ")";
            } else {
                throw new Exception("Malformed search query: " . $terms);
            }
        }

        return $query;
    }

    /**
     *  performs an LDAP search
     *
     *  @param string|array     search query
     *  @param string|array     scope to search
     *  @param array            array of field names to return (optional)
     *
     *  @throws Exception       if scope is null
     *  @throws Exception       if unable to connect to LDAP server
     *  @throws Exception       if password and username do not match
     *
     *  @return array           associative array of results
     *
     */

    public function search($terms, $scope, $fields = array()) {
        
        if ( is_null($scope) ) { throw new \Exception("No scope defined for search"); }

        // cast $scope into an array so we only have to write one piece of code
        if ( !is_array($scope) ) { $scope = (array) $scope; }

        $connection = ldap_connect($this->url, $this->port);
        if (!$connection) { throw new \Exception("Could not connect to LDAP server!"); }

        $bind = ldap_bind($connection, $this->username, $this->password);
        if (!$bind) { throw new \Exception("Incorrect Password!"); }

        $results = array();
        
        // there's probably a cleaner array_map-esque solution to this
        if ( !empty($fields) ) {
            $clean_results = array();
            
            for($i = 0; $i < $res_count; $i++ ) {
                foreach($fields as $field) {
                    $entry = $results[$i][$field];
                    if ( isset($entry['count']) && $entry['count'] == 0 ) {
                        $clean_results[$i][$field] = $entry[0];
                    } else {
                        $clean_results[$i][$field] = $entry;
                    }
                }
            }

            $results = $clean_results;
        }

        return $results;
    }

    /**
     *  sets scope to search under
     *
     *  @param string
     *
     */

    public function set_scope($scope) { $this->scope = $scope; }

    /**
     *  set instance-wide dn to associate w/ ldap query
     *  
     *  @param string   dn for user
     *  @param string   password for user
     *
     */

    public function set_user($dn, $pass) {
        $this->username = $dn;
        $this->password = $pass;
    }

    /**
     *  unset dn/pass combo
     */

    public function unset_user() {
        $this->username = null;
        $this->password = null;
    }


}