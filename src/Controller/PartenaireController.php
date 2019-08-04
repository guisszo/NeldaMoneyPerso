<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PartenaireController extends AbstractController
{
    /**
     * @Route("/admin_user", name="admin_user_reg", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function reguser(Request $request, 
    UserPasswordEncoderInterface $passwordEncoder, 
    EntityManagerInterface $entityManager, 
    SerializerInterface $serializer, 
    ValidatorInterface $validator)
    {
        $compte= new Compte();
        $values = json_decode($request->getContent());
        $repository = $this->getDoctrine()->getRepository(Compte::class);
            $cpt = $repository->find($compte->getId());
       
        if (isset($values->username, $values->password)) {
            $utilisateur = new Utilisateur();
            $utilisateur->setUsername($values->username);
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $values->password));
            $utilisateur->setRoles($values->roles);
            $utilisateur->setNomComplet($values->nomcomplet);
            $utilisateur->setTel($values->tel);
            $utilisateur->setCompte($cpt); # Insertion de l'ID du Compte
            $utilisateur->setAdresse($values->adresse);
            $utilisateur->setStatut($values->statut);
            $utilisateur->setEmail($values->email);
            $utilisateur->setcreatedAt(new \Datetime);



            $errors = $validator->validate($utilisateur);

            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->persist($utilisateur);
            $entityManager->flush();



            $data = [
                'status' => 201,
                'message' => 'L\'utilisateur a été créé'
            ];

            return new JsonResponse($data, 201);
        }
        $data = [
            'status' => 500,
            'message' => 'Vous devez renseigner les clés username et password'
        ];
        return new JsonResponse($data, 500);
    }
    
    /************************** modification d'un Partenaire_user ******************/

    /*
     * @Route("/modif_partuser/{id}" , name="modif_partuser" , methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
    */

    public function partUserModif(Request $request, SerializerInterface $serializer, Partenaire $part, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $partUpdate = $entityManager->getRepository(Partenaire::class)->find($part->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value){
            if($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set'.$name;
                $partUpdate->$setter($value);
            }
        }  
        $errors = $validator->validate($partUpdate);
        if(count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200, 
            'message' => 'Le user a bien été mis à jour'
        ];
        return new JsonResponse($data);
    }
}
