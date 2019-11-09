<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/admin")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/delete/user/{id}", name="delete_user", methods={"POST"})
     */
    public function deleteUser($id, UserRepository $userRepository, SerializerInterface $serializer, Request $request)
    {
        //je récupere les info avec getContent
        $request->getContent();
        //je recherche les utilisateurs par id
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        // suppression de l'utilisateur puis flush
        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse('User deleted');
    }

    /**
    * @Route("/users", name="all_Users", methods="GET")
    */
    public function allUsers(UserRepository $repository, SerializerInterface $serializer)
    {
        //je récupere toutes les place
        $users = $repository->findAll();
        $jsonData = $serializer->serialize($users, 'json', ['groups' => 'users_get']);
        return JsonResponse::fromJsonString($jsonData);
    }

    /**
     * @Route("/edit/user/{id}", name="edit_user", methods={"PUT"})
     */
    public function editUser($id , Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $encoder)
    {
        $jsonContent = $request->getContent();
        // recheche les places par id dans la bdd
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        // deserizlize le json dans un object existant
        $place = $serializer->deserialize(
            $jsonContent,
            User::class,
            'json',
            ['object_to_populate' => $user]
        );
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
        // je récupere le mdp de l'utilisateur
        $password = $user->getPassword();
        //on l'encode avec le passwordEncoder
        $encodedPassword = $encoder->encodePassword($user, $password);
        // On écrase le mot de passe avec le mot de passe encodé
        $user->setPassword($encodedPassword);
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        // on retourne une réponse au front
        return  new JsonResponse('user updated');  
    }
}
