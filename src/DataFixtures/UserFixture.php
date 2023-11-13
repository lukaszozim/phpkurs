<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use App\Enum\AddressTypes;
use App\Service\UserServices;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class UserFixture extends Fixture
{

    public function __construct(private readonly UserServices $userServices)
    {

    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setPhoneNumber($faker->numberBetween(9));
            $user->setRole($this->userServices->checkRole($user));
            $user->setPassword($faker->password);

            $user->addAddress($this->generateFakeAddress($manager, $user, $faker));

            $manager->persist($user);
        }

        $manager->flush();

    }

    private function generateFakeAddress(ObjectManager $manager, User $user, Generator $faker): Address
    {
        $address = new Address();

        $address->setCity($faker->city);
        $address->setType($this->generateRandomAddressType());
        $address->setZipCode($faker->postcode);
        $address->setStreet($faker->streetName);
        $address->setUser($user);

        $manager->persist($address);

        return $address;

    }

    private function generateRandomAddressType()
    {
        return AddressTypes::getAllValues()[array_rand(AddressTypes::getAllValues())]->name;

    }

}