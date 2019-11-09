<?php

namespace App\Controller;

use DateTime;
use App\Entity\City;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
    * @Route("/create/user", name="create_user", methods={"POST"})
    */
    public function createUser(UserRepository $repository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        // je récupere le json depuis insomnia avec getContent()
        $jsonContent = $request->getContent();
        // je deserialize les donnée dans mon entitée User
        $user = $serializer->deserialize($jsonContent, User::class, 'json');
        //role user par défaut
        $roles = array('ROLE_USER');
        $user->setRoles($roles);
        // je récupere le mdp de l'utilisateur
        $password = $user->getPassword();
        //on l'encode avec le passwordEncoder
        $encodedPassword = $encoder->encodePassword($user, $password);
        // On écrase le mot de passe avec le mot de passe encodé
        $user->setPassword($encodedPassword);
        // On valide l'entité
        $errors = $validator->validate($user);
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
        $entityManager->persist($user);
        $entityManager->flush();
        // on retourne une réponse au front
        return  new JsonResponse('new user create');
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        // je récupere le json depuis insomnia avec getContent()
        $jsonContent = $request->getContent();
        $parsed_json = json_decode($jsonContent);
        $email = $parsed_json->{'email'};
        $password = $parsed_json->{'password'};
        $userEmail = $userRepository->findOneByEmail($email);
        if ($userEmail === null){
            return  new JsonResponse('Email don\'t exist');
        }
        else {
            $jsonData = $serializer->serialize($userEmail, 'json', ['groups' => 'users_get']);
            return JsonResponse::fromJsonString($jsonData);
        }
    }
}