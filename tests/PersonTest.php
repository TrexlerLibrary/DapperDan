<?php
require "Person.php";
class PersonTest extends PHPUnit_Framework_TestCase {

    public $person;
    public $person_array;

    public function setUp() {
        $this->person_array = array(
            "fn" => "Max",
            "sn" => "Roach",
            "email" => "maxroach@debutrecords.com"
        );

        $this->person = new DapperDan\Person($this->person_array);
    }

    public function test_get_value() {
        $this->assertEquals($this->person_array['fn'], $this->person->get_value('fn'));
        $this->assertEquals($this->person_array, $this->person->get_value(array_keys($this->person_array)));
    }

    public function test_has_key() {
        $this->assertTrue($this->person->has_key('email'));
        $this->assertFalse($this->person->has_key('dn'));
    }

}