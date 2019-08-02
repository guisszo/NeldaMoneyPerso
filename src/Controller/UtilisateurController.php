<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\Utilisateur;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use PhpParser\Node\Stmt\TryCatch;

/**
 * @Route("/api")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/regpart", name="registerpartenaire", methods={"POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PartenaireRepository $repository
    ) {
        $values = json_decode($request->getContent());

        /********************** Insertion Partenaire ***********************/

        $partenaire = new Partenaire();
        $partenaire->setRaisonSociale($values->raison_sociale);
        $partenaire->setNinea($values->ninea);

        $entityManager->persist($partenaire);
        $entityManager->flush();


        if ($partenaire) {
            #recupération de l'ID du Partenaire

            $repository = $this->getDoctrine()->getRepository(Partenaire::class);
            $part = $repository->find($partenaire->getId());

            /************ insertion compte *********************/

            $compte = new Compte();
            $min=1000000000;
            $max=9999999999;
            $compte_rand=rand($min,$max);
            $compte->setNumcompte($compte_rand);
            $compte->setSolde(0);
            $compte->setPartenaire($part); # Insertion de l'ID du partenaire
            $entityManager->persist($compte);
            $entityManager->flush();

            /************** insertion Utilisateur *****************/

            if ($compte) {
                #recupération de l'ID du Compte

                $repository = $this->getDoctrine()->getRepository(Compte::class);
                $cpt = $repository->find($compte->getId());


                if (isset($values->username, $values->password)) {
                    $utilisateur = new Utilisateur();
                    $utilisateur->setUsername($values->username);
                    $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $values->password));
                    $utilisateur->setRoles($values->roles);
                    $utilisateur->setPartenaire($part); # Insertion de l'ID du Partenaire
                    $utilisateur->setCompte($cpt); # Insertion de l'ID du Compte
                    $utilisateur->setNomComplet($values->nomcomplet);
                    $utilisateur->setTel($values->tel);
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
                        'message' => 'Le partenaire a été créé'
                    ];

                    return new JsonResponse($data, 201);
                }
                $data = [
                    'status' => 500,
                    'message' => 'Vous devez renseigner les clés username et password'
                ];
                return new JsonResponse($data, 500);
            }
        }
    }

    /**
     * @Route("/registeruser", name="registeruser", methods={"POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function reguser(Request $request, 
    UserPasswordEncoderInterface $passwordEncoder, 
    EntityManagerInterface $entityManager, 
    SerializerInterface $serializer, 
    ValidatorInterface $validator)
    {
        $values = json_decode($request->getContent());
       
        if (isset($values->username, $values->password)) {
            $utilisateur = new Utilisateur();
            $utilisateur->setUsername($values->username);
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $values->password));
            $utilisateur->setRoles($values->roles);
            $utilisateur->setNomComplet($values->nomcomplet);
            $utilisateur->setTel($values->tel);
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
}
