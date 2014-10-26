<?php
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

    public function test_get() {
        $this->assertEquals($this->person_array['fn'], $this->person->get('fn'));
        $this->assertEquals($this->person_array, $this->person->get(array_keys($this->person_array)));
    }

    public function test_has() {
        $this->assertTrue($this->person->has('email'));
        $this->assertFalse($this->person->has('dn'));
    }

}