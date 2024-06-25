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
    public function creerParticipants(ObjectManager $manager){
        $participants = new Participant();
        $participants->setNom("admin");
        $participants->setPrenom("admin");
        $participants->setEmail("admin@gmail.com");
        $participants->setTelephone("0123456789");
        $participants->setPseudo("admin");
        $participants->setPassword("admin");
        $participants->setRoles(["ROLE_ADMIN"]);
        $participants->setDateCreation(\DateTime::createFromFormat('d/m/Y', now()->format('d/m/Y')));
        $manager->persist($participants);

        for($i = 0; $i < 100; $i++){
            $participants = new Participant();
            $participants->setNom("user".$i)
                ->setPrenom("user".$i)
                ->setEmail("user".$i."@gmail.com")
                ->setTelephone("0123456789")
                ->setPseudo("user".$i)
                ->setPassword("user")
                ->setRoles(["ROLE_USER"])
                ->setDateCreation(\DateTime::createFromFormat('d/m/Y', now()->format('d/m/Y')));;
            $manager->persist($participants);
        }

        $manager->flush();
    }
}
