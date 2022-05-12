<?php

namespace App\Controller;

use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    #[Route('/api/regions', name: 'addAll_regions')]
    public function addRegionByApi(SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        //Récupération des régions en JSON
        $regionJson = file_get_contents("https://geo.api.gouv.fr/regions");
        //dd($regionJson);

        //Methode 1
        //Decode JSON to array
        //$regionTab = $serializer->decode($regionJson, "json");
        //dd($regionTab);

        //Denormalize Array to Object
        //$regionObject = $serializer->denormalize($regionTab, "App\Entity\Region[]");
        //dd($regionObject);

        //Methode 2
        //Decode JSON to Object
        $regionObject = $serializer->deserialize($regionJson, "App\Entity\Region[]", "json");
        //dd($regionObject);

        foreach ($regionObject as $region) {
            $em->persist($region);
        }

        $em->flush();

        return new JsonResponse("Succès, bien enregistré!", Response::HTTP_CREATED, [], true);
    }



    #[Route('/api/show/regions', name: 'show_regions')]
    public function showRegionByApi(SerializerInterface $serializer, RegionRepository $regionRepository): Response
    {
        $regionsObject = $regionRepository->findAll();

        $regionsJson = $serializer->serialize(
            $regionsObject,
            'json',
            [
                "groups" => ['region:read']
            ]
        );

        return new JsonResponse($regionsJson, Response::HTTP_CREATED, [], true);
    }


    #[Route('/api/post_region', name: 'api_post_region')]
    public function addRegion(SerializerInterface $serializer, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {

        $region = $request->getContent();

        $regionObject = $serializer->deserialize($region, "App\Entity\Region", "json");

        //Validation des données
        $errors = $validator->validate($regionObject);

        if (count($errors) > 0) {
            $errorString = $serializer->serialize($errors, "json");
            return new JsonResponse($errorString, Response::HTTP_BAD_REQUEST, [], true);
        }


        $em->persist($regionObject);
        $em->flush();

        return new JsonResponse("Bien enregistre", Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/post_department', name: 'api_post_department')]
    public function addDepartement(Request $request,ValidatorInterface $validator,SerializerInterface $serializer,RegionRepository $repo) {
        
        //Recuperation du Contenu Json
        $departementJson = $request->getContent();
        
        //Transformation du contenu en Tableau
        $departementTable = $serializer->decode($departementJson, "json");
        
        //Recuperation de l'objet Region
        
        $region = $repo->findBy((int)$departementTable["region"]["id"]);
        
        
        $departementsObject = $serializer->deserialize($request->getContent(), Departement::class, 'json');
        $departementsObject->setRegion($region);
        
        $errors = $validator->validate($departementsObject);
        
        if (count($errors) > 0) {
            $errorsString = $serializer->serialize($errors, "json");
            
            return new JsonResponse($errorsString, Response::HTTP_BAD_REQUEST, [], true);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($departementsObject);
        $entityManager->flush();
        
        return new JsonResponse("succes", Response::HTTP_CREATED, [], true);
    }




    #[Route('/api/departments', name: 'add_departments')]
    public function addDepartmentByApi(SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        //Récupération des régions en JSON
        $departmentJson = file_get_contents("https://geo.api.gouv.fr/regions");
        $departmentObject = $serializer->deserialize($departmentJson, "App\Entity\Department[]", "json");

        foreach ($departmentObject as $department) {
            $em->persist($department);
        }

        $em->flush();

        return new JsonResponse("Succès, bien enregistré!", Response::HTTP_CREATED, [], true);
    }
}
