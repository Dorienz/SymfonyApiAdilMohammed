<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Profil;
use App\Entity\Commune;
use App\Entity\Department;
use App\Repository\RegionRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    function __construct(RegionRepository $regionRepository, UserPasswordHasherInterface $passwordHasher )
    {
        $this->regionRepository = $regionRepository;
        $this->userPasswordHasherInterface = $passwordHasher;
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

        $profils = ['ADMIN', 'FORMATEUR', 'APPRENANT', 'CM'];
      
        foreach ($profils as $key => $libelle) {
            $profil = new Profil();
            $profil->setLibelle($libelle);
            $manager->persist($profil);
            $manager->flush();

            for ($i=0; $i <=3 ; $i++) { 
                $user = new User();
                $user->setProfil($profil)
                     ->setLogin(strtolower($libelle).$i)
                     ->setNomComplet($faker->name());
            $hasher = $this->userPasswordHasherInterface->hashPassword($user, 'passer123');
            $user->setPassword($hasher);
            $manager->persist($user);
            }
            $manager->flush();
        }
    }   
}

