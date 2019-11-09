<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Place;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CityController extends AbstractController
{
    /**
     * @Route("/city", name="all_city", methods={"GET"})
     */
    public function allCity(CityRepository $repository, SerializerInterface $serializer)
    {
        //je récupere toutes les villes
        $city = $repository->findAll();
        $jsonData = $serializer->serialize($city, 'json', ['groups' => 'city_get']);
        return JsonResponse::fromJsonString($jsonData);
    }

    /**
     * @Route("/search/{nameCity}", name="search_PlaceByCity", methods={"GET"})
     */
    public function searchPlaceByCity($nameCity, SerializerInterface $serializer)
    {
        //I search the city in DB
        $cityResult = $this->getDoctrine()
        ->getRepository(City::class)
        ->findOneBy(['name' => $nameCity]);
        //if the city don't existe in DB i return json message error
        if (!$cityResult){
            return JsonResponse::fromJsonString('this city don\'t exist or haven\'t places');
        }
        //else i search all place collection with the relation of the city
        $placesCity = $this->getDoctrine()
        ->getRepository(Place::class)
        ->findBy([
            'city' => $cityResult
            //validate => true
        ]);
        //I prepare the json answer forma
        $jsonPlaces = $serializer->serialize($placesCity, 'json', ['groups' => 'places_get']);
        //And i return the json as answer
        return JsonResponse::fromJsonString($jsonPlaces);
    }

    /**
     * @Route("/create/city", name="create_city", methods={"POST"})
     */
    public function createCity(CityRepository $city, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        // je récupere le json depuis insomnia avec getContent()
        $jsonContent = $request->getContent();
        // je deserialize les donnée dans mon entitée Place
        $newCity = $serializer->deserialize($jsonContent, City::class, 'json');
        // On valide l'entité
        $errors = $validator->validate($newCity);
        // En cas d'erreurs
        if (count($errors) > 0) {
            // On reconstitue un tableau à partir des erreurs
            // pour informer le front
            $jsonErrors = [];
            foreach ($errors as $error) {
                // Propriété de l'entité en erreur + message d'erreur
                $jsonErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json($jsonErrors, 422);
        }
        // On save en BDD
        $entityManager->persist($newCity);
        $entityManager->flush();
        // on retourne une réponse au front
        return  new JsonResponse('new City create');
    }
    
}