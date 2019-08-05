<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\Utilisateur;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use App\Form\PartenaireType;
use App\Form\UtilisateurControllerFormType;

/**
 * @Route("/api")
 */
class UtilisateurController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
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
        /*$values = json_decode($request->getContent());*/

        $values= $request->request->all();
        // var_dump($values);die();

        /********************** Insertion Partenaire ***********************/

        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->submit($values);

        $entityManager->persist($partenaire);
        $entityManager->flush();


        if ($partenaire) {
            #recupération de l'ID du Partenaire

            $repository = $this->getDoctrine()->getRepository(Partenaire::class);
            $part = $repository->find($partenaire->getId());

            /************ insertion compte *********************/

            $compte = new Compte();
            $min = 10000;
            $max = 99999;
            $date       = new \DateTime;
            $debutNum   = $date->format('ydHs');
            $compte_rand = rand($min, $max) . $debutNum;

            $compte->setNumcompte($compte_rand);
            $compte->setSolde(0);
            $compte->setPartenaire($part); # Insertion de l'ID du partenaire
            $entityManager->persist($compte);
            $entityManager->flush();

            /************** insertion Utilisateur *****************/

            if ($compte) {

                # recupération de l'ID du Compte

                $repository = $this->getDoctrine()->getRepository(Compte::class);
                $cpt = $repository->find($compte->getId());


                    $utilisateur = new Utilisateur();
                    $form = $this->createForm(UtilisateurControllerFormType::class, $utilisateur);
                    $file=$request->files->all()['imageName'];
                    $form->submit($values);
                  
                    $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur,$form->get('password')->getData()));
                    $utilisateur->setRoles(['ROLE_ADMIN']);
                    $utilisateur->setPartenaire($part); # Insertion de l'ID du Partenaire
                    $utilisateur->setCompte($cpt); # Insertion de l'ID du Compte
                    $utilisateur->setStatut('actif');
                    $utilisateur->setImageFile($file);
                    $utilisateur->setcreatedAt(new \Datetime);
                    $utilisateur->setUpdatedAt(new \Datetime);

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
                        'statut' => 201,
                        'msge' => 'Le partenaire a été créé'
                    ];

                    return new JsonResponse($data, 201);
               
            }
        }
    }


    /************** Insertion d'un utilisateur simple par super_admin********** */

    /**
     * @Route("/registeruser", name="registeruser", methods={"POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function reguser(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        //$values = json_decode($request->getContent());
         $values= $request->request->all();


            $utilisateur = new Utilisateur();
            $form = $this->createForm(UtilisateurControllerFormType::class, $utilisateur);
            $file=$request->files->all()['imageName'];
            $form->submit($values);

            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $form->get('password')->getData()));
            $utilisateur->setRoles(['ROLE_ADMIN']);
            $utilisateur->setStatut('actif');
            $utilisateur->setImageFile($file);
            $utilisateur->setcreatedAt(new \Datetime);
            $utilisateur->setUpdatedAt(new \Datetime);



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

    /************************** modification d'un user ******************/

    /*
     * @Route("/modif_user/{id}" , name="modif_user" , methods={"PUT"})
     * @IsGranted("ROLE_ADMIN")
    */

    public function partUserModif(Request $request, SerializerInterface $serializer, Partenaire $part, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $partUpdate = $entityManager->getRepository(Partenaire::class)->find($part->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                $name = ucfirst($key);
                $setter = 'set' . $name;
                $partUpdate->$setter($value);
            }
        }
        $errors = $validator->validate($partUpdate);
        if (count($errors)) {
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

    /************************ Authentification ************************/

    
    /**
     *  @Route("/login_check", name="login", methods={"POST"})
     * @param Request $request
     * @param JWTEncoderInterface $JWTEncoder
     * @return JsonResponse
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function token(Request $request, JWTEncoderInterface $JWTEncoder)
    {
        $values = json_decode($request->getContent());

        $username= $values->username;
        $mdp= $values->password;

        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy([
            'username' => $username
        ]);

        if (!$user) {
            throw $this->createNotFoundException('cette utilisateur n\'existe pas');
        }

        $isValid = $this->encoder->isPasswordValid($user, $mdp);
        if (!$isValid) {
            throw new BadCredentialsException();
        }
        $token = $JWTEncoder->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 7200 // 1 hour expiration
            ]);

        return new JsonResponse(['token' => $token]);
    }

    // /*************** Depot par le caissier *****************/

    /**
     * @Route("/depot" , name="depot" , methods={"POST"})
     * @IsGranted("ROLE_CAISSIER")
     */
    public function depot(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();


        $values = json_decode($request->getContent());
        try {
            if ($values->montant < 75000) {
                $exception = [
                    'status' => 500,
                    'message' => 'le montant doit etre supérieur a 75000 FCFA'
                ];
                return new JsonResponse($exception, 500);
            }
            $repo = $this->getDoctrine()->getRepository(Compte::class);
            $compte = $repo->findOneBy(['numcompte' => $values->numcompte]);
            $solde = $compte->getSolde();
            $compte->setSolde($values->montant + $solde);
            $entityManager->persist($compte);
            $entityManager->flush();
        } catch (ParseException $exception) {
            $exception = [
                'status' => 500,
                'message' => 'Vous devez renseigner les tous  champs'
            ];
            return new JsonResponse($exception, 500);
        }


        if (isset($compte)) {
            $depot = new Depot();
            $depot->setCaissier($user);
            $depot->setMontant($values->montant);
            $depot->setMtnAvantDepot($solde);
            $depot->setCreatedAt(new \DateTime);


            $repo = $this->getDoctrine()->getRepository(Compte::class);
            $Comp = $repo->find($compte);
            $depot->setCompte($Comp);
            $entityManager->persist($depot);
            $entityManager->flush();

            $errors = $validator->validate($depot);

            $errors = $validator->validate($Comp);
            if (count($errors)) {
                $errors = $serializer->serialize($errors, 'json');
                return new Response($errors, 500, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $entityManager->flush();
            $data = [
                'status' => 200,
                'message' => 'Le depot a éte fait avec succes ' . 'par ' . $user->getNomcomplet()
            ];
            return new JsonResponse($data);
        }
    }
}
