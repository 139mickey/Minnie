<?php
abstract class Animal {
    var $name = "I'm Animal ";

    public function __construct($name = null){
        $this->name = $name ? $name : $this->name;
    }

    public function foodType(){
        echo "I like anything ";
    }

    public function whoAmI(){
        echo $this->name;
    }
}

trait Carnivore {
    var $someTrait = "here is some trait";
        public function whoAmI(){
            echo "I like burger ";
        }
}

class Cat extends Animal {

    var $someTrait = "here is some strait";
    use Carnivore;

    public function wwhoAmI(){
        echo "I am a cat ";
    }

    public function foodType(){
        echo "I like finish ";
        echo "and i like ". $this->someTrait;
    }

}

$cat = new Cat();
$cat->whoAmI();
$cat->foodType();
