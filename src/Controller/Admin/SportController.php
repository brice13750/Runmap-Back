<?php

namespace App\Controller\Admin;

use App\Entity\Sport;
use App\Repository\SportRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/admin")
 */
class SportController extends AbstractController
{
    /**
     * @Route("/delete/sport/{id}", name="delete_sport", methods={"POST"})
     */
    public function deleteSport($id, SportRepository $sportRepository, SerializerInterface $serializer, Request $request)
    {
        //je récupere les info avec getContent
        $request->getContent();
        //je recherche les places par id
        $entityManager = $this->getDoctrine()->getManager();
        $sport = $entityManager->getRepository(Sport::class)->find($id);
        // suppression de la place puis flush
        $entityManager->remove($sport);
        $entityManager->flush();
        return new JsonResponse('Sport deleted');
    }
}
