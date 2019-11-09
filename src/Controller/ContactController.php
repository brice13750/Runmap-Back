<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request, SerializerInterface $serializer, \Swift_Mailer $mailer)
    {
        $jsonContent = $request->getContent();

        //I translate the json of the request in a array
        $contact = json_decode($jsonContent);
        
        //I take the value of the json_dcode array
        $email = $contact->email;
        $subject = $contact->subject;
        $body = $contact->body;

        $message = (new \Swift_Message($subject))
        ->setFrom($email)
        ->setTo('runmap.project@gmail.com')
        ->setBody($body);

        $mailer->send($message);

        return new JsonResponse("message sent");
    }
}
