<?php

/**
 * Przykład zastosowania interfejsów
 */
interface AnimalInterface {
    public function action();
}
class Bird implements AnimalInterface {
    public function action() {
        echo 'ACTION BIRD:: FLY' . PHP_EOL;
    }
}

class Dog implements AnimalInterface{
    public function action()
    {
        echo 'ACTION DOG:: BARK' . PHP_EOL;
    }
}
class Service {

    public function action(AnimalInterface $animal) {
        $animal->action();
        // tutaj moga byc inne metody z animal interface np w jakiejsc konkretnej kolejnosci??
    }
}

$bird = new Bird();
$dog = new Dog();

$service = new Service();
$service->action($bird); // rozumiem, ze to jest to samo co $bird->action();??
$service->action($dog);

//--------------------------------------------
/**
 * Analogiczny przykład z zastosowaniem klasy abstrakcyjnej
 */

abstract class AbstractVehicle {
    public function run()//funkcja współna dla wszystkich klas potomnych
    {
        echo 'JEDZIE' . PHP_EOL;
    }
    public abstract function getName();

}

class Car extends AbstractVehicle {
    public function getName()
    {
        echo __CLASS__ . PHP_EOL;
    }
}

class Motor extends AbstractVehicle {
    public function getName()
    {
        echo __CLASS__ . PHP_EOL;
    }
}
class VehicleService {

    public function execute(AbstractVehicle $vehicle) {
        $vehicle->getName();
        $vehicle->run();
    }
}

$car = new Car();
$motor = new Motor();

$service = new VehicleService();
$service->execute($car);
$service->execute($motor);
