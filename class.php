<?php
class A {
    public $firstname;
    public $lastname;

    public function __construct() {

    }
    public function getFirstName(){

        return $this->firstname;

    }
    public function getLastName(){

        return $this->lastname;

    }
}
class B extends A{
    public $name;
    public function __construct($name) {
        parent::__construct($name);
    }
}
$a = new A('Hittesh');
var_dump($a);

$b = new B('Kannchan');
var_dump($b);

