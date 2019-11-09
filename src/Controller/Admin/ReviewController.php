<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/admin")
 */
class ReviewController extends AbstractController
{
    /**
     * @Route("/delete/review/{id}", name="delete_review", methods={"POST"})
     */
    public function deleteReview($id, ReviewRepository $reviewRepository, SerializerInterface $serializer, Request $request)
    {
        //je récupere les info avec getContent
        $request->getContent();
        //je recherche les reviews par id
        $entityManager = $this->getDoctrine()->getManager();
        $review = $entityManager->getRepository(Review::class)->find($id);
        // suppression de l'avis puis flush
        $entityManager->remove($review);
        $entityManager->flush();
        return new JsonResponse('Review deleted');
    }
}