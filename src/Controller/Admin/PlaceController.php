<?php

namespace App\Controller\Admin;

use App\Entity\Place;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/admin")
 */
class PlaceController extends AbstractController
{
    /**
     * @Route("/delete/place/{id}", name="delete_place", methods={"POST"})
     */
    public function deletePlace($id, PlaceRepository $placeRepository, SerializerInterface $serializer, Request $request)
    {
        //je récupere les info avec getContent
        $request->getContent();
        //je recherche les places par id
        $entityManager = $this->getDoctrine()->getManager();
        $place = $entityManager->getRepository(Place::class)->find($id);
        // suppression de la place puis flush
        $entityManager->remove($place);
        $entityManager->flush();
        return new JsonResponse('Place deleted');
    }
}
