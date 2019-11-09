<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/admin")
 */
class CityController extends AbstractController
{
    /**
     * @Route("/delete/city/{id}", name="delete_city", methods={"POST"})
     */
    public function deleteCity($id, CityRepository $cityRepository, SerializerInterface $serializer, Request $request)
    {
        //je récupere les info avec getContent
        $request->getContent();
        //je recherche les villes par id
        $entityManager = $this->getDoctrine()->getManager();
        $city = $entityManager->getRepository(City::class)->find($id);
        // suppression de la ville puis flush
        $entityManager->remove($city);
        $entityManager->flush();
        return new JsonResponse('City deleted');
    }

    /**
     * @Route("/edit/city/{id}", name="edit_city", methods={"PUT"})
     */
    public function editCity($id , Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();
        // recheche les places par id dans la bdd
        $city = $this->getDoctrine()->getRepository(City::class)->find($id);
        // deserizlize le json dans un object existant
        $city = $serializer->deserialize(
            $jsonContent,
            City::class,
            'json',
            ['object_to_populate' => $city]
        );
        // On valide l'entité
        $errors = $validator->validate($city);
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
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($city);
        $entityManager->flush();
        // on retourne une réponse au front
        return  new JsonResponse('city updated');  
    }
}
