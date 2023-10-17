<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < 10; $i++) {
            $this->createData($manager);
        }
    }

    protected function createData(ObjectManager $manager){
        $faker = Factory::create();
        $address = new Address();
        $address
            ->setPostalCode($faker->citySuffix)
            ->setCity($faker->city)
            ->setStreet($faker->streetName)
            ->setHouseNumber($faker->numberBetween(1,100))
            ->setApartmentNumber($faker->numberBetween(1,10));

        $user = new User();
        $user
            ->setFirstName($faker->firstName)
            ->setLastName($faker->lastName)
            ->setEmail($faker->email)
            ->setPassword(hash('sha256', $faker->password))
            ->setRole('SIMPLE_USER')
            ->addAddress($address);

        $manager->persist($user);
        $manager->flush();

    }
}
