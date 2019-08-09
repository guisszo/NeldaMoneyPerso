<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Profil;
use App\Entity\Partenaire;
use App\Entity\Utilisateur;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UtilisateurControllerFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class SuperAdminController extends AbstractController
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

        $values= $request->request->all();

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
            $compte->setCreatedAt(new \DateTime);
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
                    $utilisateur->setRoles(['ROLE_PARTENAIRE']);
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
     */
    public function reguser(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {

         $values= $request->request->all();
            
            $utilisateur = new Utilisateur();
            $form = $this->createForm(UtilisateurControllerFormType::class, $utilisateur);
            $file=$request->files->all()['imageName'];
            $form->submit($values);
            $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $form->get('password')->getData()));
            
            $repository = $this->getDoctrine()->getRepository(Profil::class);
                $profils = $repository->find($values['profil']);
                $utilisateur->setProfil($profils);

            if($profils->getLibelle()=="admin"){

                if($this->getUser()->getRoles()[0]!="ROLE_SUPER_ADMIN" &&
                $this->getUser()->getRoles()[0]!= "ROLE_ADMIN"
                ){

                    $data = [
                        'statut' => 401,
                        'msge3' => 'acces refuse, vous etes pas autorise a faire cette action'
                    ];

                    return new JsonResponse($data, 401);
                }

                $utilisateur->setRoles(['ROLE_ADMIN']);

            }elseif($profils->getLibelle()=="caissier"){
               
                if($this->getUser()->getRoles()[0]!="ROLE_SUPER_ADMIN" &&
                $this->getUser()->getRoles()[0] !="ROLE_ADMIN"
                ){

                   $data = [
                        'statut' => 401,
                        'msge4' => 'acces refuse, vous etes pas autorise a faire cette action'
                    ];

                    return new JsonResponse($data, 401);
                }
                $utilisateur->setRoles(['ROLE_CAISSIER']);

            }elseif($profils->getLibelle()=="partenaire_admin"){

                if($this->getUser()->getRoles()[0]!="ROLE_PARTENAIRE" &&
                $this->getUser()->getRoles()[0] !="ROLE_PARTENAIRE_ADMIN"
                ){
                    
                    $data = [
                        'statut' => 401,
                        'msge1' => 'acces refuse, seul un partenrie ou un partenaire_admin peuvent effectuer cette action'
                    ];

                    return new JsonResponse($data, 401);
                }
               $users=$this->getUser()->getPartenaire();

               $utilisateur->setPartenaire($users);
                $utilisateur->setRoles(['ROLE_PARTENARE_ADMIN']);


            }elseif($profils->getLibelle()=="user"){

                if($this->getUser()->getRoles()[0]!="ROLE_PARTENAIRE" &&
                $this->getUser()->getRoles()[0] !="ROLE_PARTENAIRE_ADMIN"
                ){
                    
                    $data = [
                        'statut' => 401,
                        'msge1' => 'acces refuse, seul un partenrie ou un partenaire_admin peuvent effectuer cette action'
                    ];

                    return new JsonResponse($data, 401);
                }
                $users=$this->getUser()->getPartenaire();
               
               $utilisateur->setPartenaire($users);
                $utilisateur->setRoles(['ROLE_USER']);
            }

       
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
                'stts' => 201,
                'me' => 'L\'utilisateur a été créé'
            ];

            return new JsonResponse($data, 201);
       
    }

    /************************** modification d'un user ******************/


    /**
     * @Route("/modif_user/{id}" , name="modif_user" , methods={"PUT"})
     * @IsGranted("ROLE_SUPER_ADMIN")
    */
    public function update($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);
    
        if (!$utilisateur) {
            throw $this->createNotFoundException(
                'pas d\'utilisateur trouve pour cet id '.$id
            );
        }
    
        $utilisateur->setStatut('bloque');
        $entityManager->flush();
    
        $data = [
                    'sta' => 200,
                    'mes' => 'L\'utilisateur a bien ete mis a jour'
                ];
                return new JsonResponse($data);
    }

    /********************************************************/
    /**
     * @Route("/createCpt", name= "crationCompte", methods={"POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function creationCompte(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PartenaireRepository $repository
    ){
        #################recupération de l'ID du Partenaire####################

        $values = json_decode($request->getContent());
        $repository = $this->getDoctrine()->getRepository(Partenaire::class);
    
        $partenaire = $repository->findOneBy(['ninea' => $values->ninea]);
        $user= $this->getUser();
      
        if ($partenaire) {
          $compte= new Compte();
          $min = 10000;
          $max = 99999;
          $date       = new \DateTime;
          $concat   = $date->format('ydHs');
          $compte_rand = rand($min, $max) . $concat;
          $compte->setNumcompte($compte_rand);
          $compte->setSolde(0);
          $compte->setPartenaire($partenaire); # Insertion de l'ID du partenaire
          $compte->setCreatedAt(new \DateTime);
          $entityManager->persist($compte);
          $entityManager->flush();
          $data = [
            'sta' => 201,
            'mes' => 'Le nouveau compte a ete cree par '.$user->getNomcomplet()
        ];
        return new JsonResponse($data);
        }else 
        {
          $data = [
              'st' => 500,
              'mes' => 'ce partenaire n\'existe pas'
          ];
          return new JsonResponse($data);
        }
      
    }
}