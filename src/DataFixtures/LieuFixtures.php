<?php

namespace App\DataFixtures;

use App\Controller\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class LieuFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(){
        $faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $this->creerLieux($manager);

    }

    public function creerLieux(ObjectManager $manager){
        for ($i = 0; $i < 20; $i++) {

            $lieu = new Lieu();

            $lieu->setIdLieu($this->faker->randomDigit());
            $lieu->setNom($this->faker->name);
            $lieu->setRue($this->faker->streetAddress);
            $lieu->setLatitude($this->faker->latitude);
            $lieu->setLongitude($this->faker->longitude);

            $manager->persist($lieu);
        }

        $manager->flush();
    }

}
