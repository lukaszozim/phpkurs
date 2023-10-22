<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

;

class UserFixture extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['user'];
    }


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i =1; $i < 100; $i++){
            $user = new User();
            $user
                ->setRole('ADMIN')
                ->setPassword(hash('sha256', $faker->password))
                ->setEmail($faker->email)
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setPhoneNumber($faker->numberBetween(100000000,999999999));
            $manager->persist($user);
            $manager->flush();
        }
    }
}
