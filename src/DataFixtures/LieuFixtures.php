<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use function Symfony\Component\Clock\now;

class LieuFixtures extends Fixture
{
    private readonly Generator $faker;

    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $this->creerLieux($manager);
        $this->creerParticipants($manager);
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
