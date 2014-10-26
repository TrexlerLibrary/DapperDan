<?php
class ServerTest extends PHPUnit_Framework_TestCase {
    
    public $dan;

    public function setUp() {
        $this->dan = new DapperDan\Server("ldap.example.com");
    }

    public function test_prep_query_single() {
        $correct_single_query = "(dn=AbbeyLincoln)";
        $single_array = array("dn" => "AbbeyLincoln");
        $single_terms = "dn=AbbeyLincoln";

        $this->assertEquals($correct_single_query, $this->dan->prep_query($single_array));
        $this->assertEquals($correct_single_query, $this->dan->prep_query($single_terms));
    }

    public function test_prep_query_multiple() {
        $correct_and_query    = "(&(dn=AbbeyLincoln)(ln=Chicago,Illinois))";
        $multiple_and_array = array("dn" => "AbbeyLincoln", "ln" => "Chicago,Illinois", "operator" => "&");

        $correct_or_query = "(|(dn=MaxRoach)(dn=AbbeyLincoln))";
        $multiple_or_array = array("dn" => array("MaxRoach", "AbbeyLincoln"), "operator" => "|");

        $this->assertEquals($correct_and_query, $this->dan->prep_query($multiple_and_array));
        $this->assertEquals($correct_or_query, $this->dan->prep_query($multiple_or_array));
    }
}