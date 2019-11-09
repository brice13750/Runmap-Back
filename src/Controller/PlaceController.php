<?php

namespace App\Controller;
use App\Entity\Place;
use App\Entity\City;
use App\Form\PlaceType;
use App\Repository\CityRepository;
use App\Repository\PlaceRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManager;


class PlaceController extends AbstractController
{

    /**
    * @Route("/places", name="Places")
    */
    public function allPlace(PlaceRepository $repository, SerializerInterface $serializer)
    {
        //je récupere toutes les place
        $places = $repository->findAll();

        $jsonData = $serializer->serialize($places, 'json', ['groups' => 'places_get']);
 
        return JsonResponse::fromJsonString($jsonData);
    }

    /**
     * @Route("/place/{id}", name="place")
     */
    public function showPlace(Place $place, SerializerInterface $serializer)
    {
        // if (!$place) {
        //     throw $this->createNotFoundException('la place n'existe pas);
        // }
        $jsonData = $serializer->serialize($place, 'json', ['groups' => 'places_get']);

        return JsonResponse::fromJsonString($jsonData);
    }



    /**
     * @Route("/create/place", name="new_place", methods={"POST"})
     */
    public function createPlace(CityRepository $cityRepository, PlaceRepository $place, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        // je récupere le json depuis insomnia avec getContent()
        $jsonContent = $request->getContent();
        $parsed_json = json_decode($jsonContent);
        $city = $parsed_json->{'city'}->{'name'};

        $cityName = $cityRepository->findOneByName($city);
        //return null if $city don't exist on BDD else the object city on the BDD
        if ($cityName === null){
            $newCity = new City();
            $newCityName = $parsed_json->{'city'}->{'name'};
            $newCityPostalCode = $parsed_json->{'city'}->{'postalcode'};
            $newCity->setName($newCityName);
            $newCity->setPostalcode($newCityPostalCode);
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
            $cityId = $newCity->getId();
        }
        else {
            $cityId = $cityName->getId();
        }
        $newPlace = new Place();
        $newPlaceName = $parsed_json->{'place'}->{'name'};
        $newPlaceadress = $parsed_json->{'place'}->{'adress'};
        $newPlaceComplementInfo = $parsed_json->{'place'}->{'complement_info'};
        $newPlaceLongitude = $parsed_json->{'place'}->{'longitude'};
        $newPlaceLatitude = $parsed_json->{'place'}->{'latitude'};

        $city = $cityRepository->find($cityId);

        $newPlace->setName($newPlaceName);
        $newPlace->setAdress($newPlaceadress);
        $newPlace->setComplementInfo($newPlaceComplementInfo);
        $newPlace->setCity($city);
        $newPlace->setLongitude($newPlaceLongitude);
        $newPlace->setLatitude($newPlaceLatitude);
        // On valide l'entité
        $errors = $validator->validate($newPlace);
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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($newPlace);
        $entityManager->flush();
        // on retourne une réponse au front
        return  new JsonResponse('new place create');
    }

    /**
     * @Route("/edit/place/{id}", name="edit_place", methods={"PUT"})
     */
    public function editPlace($id , Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();
        // recheche les places par id dans la bdd
        $place = $this->getDoctrine()->getRepository(Place::class)->find($id);
        // deserizlize le json dans un object existant
        $place = $serializer->deserialize(
            $jsonContent,
            Place::class,
            'json',
            ['object_to_populate' => $place]
        );
        // On valide l'entité
        $errors = $validator->validate($place);
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
        $this->getDoctrine()->getManager()->persist($place);
        $this->getDoctrine()->getManager()->flush();
        // on retourne une réponse au front

        return  new JsonResponse('place modifié');
    }
}    

