<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Place;
use App\Entity\Review;
use App\Repository\UserRepository;
use App\Repository\PlaceRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{
    /**
     * @Route("/create/review", name="create_review", methods={"POST"})
     */
    public function createReview(ReviewRepository $reviewRepository, UserRepository $userRepository, PlaceRepository $placeRepository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        //take request json post information and deserialize for instantiate a Review object
        $jsonReview = $request->getContent();
        $review = $serializer->deserialize($jsonReview, Review::class, 'json');
        //Decode jsonReview for have array with user id value and place id value
        $reviewDecode = json_decode($jsonReview);
        //use user id value for take from DB and instantiate user object
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id'=>$reviewDecode->user]);
        //use place id value for take from DB and instantiate Place object
        $place = $this->getDoctrine()
        ->getRepository(place::class)
        ->findOneBy(['id'=>$reviewDecode->place]);
        //modifiate the review with user and place object
        $review->setUser($user);
        $review->setPlace($place);

        //validation of the review entity
        $errors = $validator->validate($review);
        // if error case
        if (count($errors) > 0) {
            // error array construction
            $jsonErrors = [];
            foreach ($errors as $error) {
                $jsonErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            //return response error
            return $this->json($jsonErrors, 422);
        }
        $em = $this->getDoctrine()->getManager();
        //persist and save in DB
        $em->persist($review);
        $em->flush();
        // we return an answer to say reviews is create
        return  new JsonResponse('new review is Post in DB');
    }
}
