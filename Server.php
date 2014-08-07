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

    public function login($username, $password, $settings = array()) {
        $settings = array_merge(array(
            "search_field" => "dn"
        ), $settings);

        if ( $this->user_set() ) { $this->unset_user(); }

        // we'll need a dn entry to log in, so if one isn't provided, we'll do an anonymous search to get it
        if ( $settings['search_field'] != "dn" ) {
            $first_pass = $this->search(array("search_field" => $username));
            if ( count($first_pass) > 1 ) {
                throw new \Exception("Multiple users found under '" . $username . "'");
            } else {
                $dn = $first_pass->get_value('dn');
            }
        } else { 
            $dn = $username;
        }

        $this->set_user($dn, $password);
        return $this->search(array($settings['search_field'] => $username));

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
     *  @return Person[]        returns an array of DapperDan\Person objects
     *
     */

    public function search($terms, $scope = null) {
        
        if ( is_null($scope) && is_null($this->scope)) { throw new \Exception("No scope defined for search"); }
        if ( is_null($scope) && !is_null($this->scope) ) { $scope = $this->scope; }

        // cast $scope into an array so we only have to write one piece of code
        if ( !is_array($scope) ) { $scope = (array) $scope; }

        $query = $this->prep_query($terms);

        $connection = ldap_connect($this->url, $this->port);
        if (!$connection) { throw new \Exception("Could not connect to LDAP server!"); }

        $bind = ldap_bind($connection, $this->username, $this->password);
        if (!$bind) { throw new \Exception("Incorrect Password!"); }

        $results = array();

        foreach($scope as $s) {
            $res = ldap_search($connection, $s, $query);
            $ent = ldap_get_entries($connection, $res);
            $results = array_merge($results, $ent);
        }

        return array_map(function($user) { $user = new Person($user); }, $results);
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

    /**
     *  is an instance user set?
     *
     *  @return boolean
     */

    public function user_set() { return !is_null($this->username) && !is_null($this->password); }


}