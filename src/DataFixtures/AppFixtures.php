<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Department;
use App\Entity\Commune;
use App\Repository\RegionRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    function __construct(RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $regions = $this->regionRepository->findAll();
        
        foreach ($regions as $region) {
            $departement = new Department();
            $departement->setCode($faker->postcode())
                        ->setNom($faker->city())
                        ->setRegion($region);
            $manager->persist($departement);

            // Pour chaque département; j'insère 10 communes

            for ($i=0; $i < 10 ; $i++) { 
                $commune = new Commune();
                $commune->setCode($faker->postcode())
                        ->setNom($faker->city())
                        ->setDepartment($departement);
                $manager->persist($commune);
            }
        }


        $manager->flush();
    }
}

