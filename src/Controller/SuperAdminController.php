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
use App\Repository\ProfilRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class SuperAdminController extends AbstractController
{
    private $encoder;
    private $statut;
    private $message;
    private $constante;
    private $groups;
    private $bloquer;
private $content_type;
    private $app_json;
    private $listeUtil;
    private $rolePartAdmin;
    private $rolePart;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->statut ='statut';
        $this->message = 'message';
        $this->constante = 'actif';
        $this->groups = 'groups';
        $this->bloquer =  'bloque';
        $this->content_type = 'Content-Type';
        $this->app_json = 'application/json';
        $this->listeUtil =  'listeutile';
        $this->rolePartAdmin = "ROLE_PARTENAIRE_ADMIN";
        $this->rolePart = "ROLE_PARTENAIRE";
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
        

        $values = $request->request->all();

        /********************** Insertion Partenaire ***********************/

        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->submit($values);
        $partenaire->setStatut($this->constante);
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
                $file = $request->files->all()['imageName'];
                $form->submit($values);

                $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $form->get('password')->getData()));
                $utilisateur->setRoles(['ROLE_PARTENAIRE']);
                $utilisateur->setPartenaire($part); # Insertion de l'ID du Partenaire
                $utilisateur->setCompte($cpt); # Insertion de l'ID du Compte
                $utilisateur->setStatut( $this->constante);
                $utilisateur->setImageFile($file);
                $utilisateur->setcreatedAt(new \Datetime);
                $utilisateur->setUpdatedAt(new \Datetime);

                $errors = $validator->validate($utilisateur);

                if (count($errors)) {
                    $errors = $serializer->serialize($errors, 'json');
                    return new Response($errors, 500, [
                         $this->content_type => $this->app_json
                    ]);
                }
                $entityManager->persist($utilisateur);
                $entityManager->flush();





                $data = [
                    $this->statut => 201,
                    $this->message => 'Le partenaire a été créé'
                ];

                return new JsonResponse($data, 201);
            }
        }
    }


    /************** Insertion d'un utilisateur simple par super_admin********** */

    /**
     * @Route("/registeruser", name="registeruser", methods={"POST"})
     * IsGranted("ROLE_SUPER_ADMIN",$this->rolePart)
     */
    public function reguser(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        
        $values = $request->request->all();

        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurControllerFormType::class, $utilisateur);
        $file = $request->files->all()['imageName'];
        $form->submit($values);
        $utilisateur->setPassword($passwordEncoder->encodePassword($utilisateur, $form->get('password')->getData()));

        $repository = $this->getDoctrine()->getRepository(Profil::class);
        $profils = $repository->find($values['profil']);
        $utilisateur->setProfil($profils);

        if ($profils->getLibelle() == "admin") {

            if (
                $this->getUser()->getRoles()[0] != "ROLE_SUPER_ADMIN" &&
                $this->getUser()->getRoles()[0] != "ROLE_ADMIN"
            ) {

                $data = [
                    $this->statut => 403,
                    $this->message => 'acces refuse, vous etes pas autorise a faire cette action'
                ];

                return new JsonResponse($data, 403);
            }

            $utilisateur->setRoles(['ROLE_ADMIN']);
        } elseif ($profils->getLibelle() == "caissier") {

            if (
                $this->getUser()->getRoles()[0] != "ROLE_SUPER_ADMIN" &&
                $this->getUser()->getRoles()[0] != "ROLE_ADMIN"
            ) {

                $data = [
                    $this->statut => 403,
                    $this->message => 'acces refuse, vous etes pas autorise a faire cette action'
                ];

                return new JsonResponse($data, 403);
            }
            $utilisateur->setRoles(['ROLE_CAISSIER']);
        } elseif ($profils->getLibelle() == "partenaire_admin") {

            if (
                $this->getUser()->getRoles()[0] !== $this->rolePart &&
                $this->getUser()->getRoles()[0] !== $this->rolePartAdmin
            ) {

                $data = [
                    $this->statut => 403,
                    $this->message => 'acces refuse, seul un partenrie ou un partenaire_admin peuvent effectuer cette action'
                ];

                return new JsonResponse($data, 403);
            }
            $users = $this->getUser()->getPartenaire();

            $utilisateur->setPartenaire($users);
            $utilisateur->setRoles([$this->rolePartAdmin]);
        } elseif ($profils->getLibelle() == "user") {

            if (
                $this->getUser()->getRoles()[0] != $this->rolePart &&
                $this->getUser()->getRoles()[0] != $this->rolePartAdmin
            ) {

                $data = [
                    $this->statut => 403,
                    $this->message => 'acces refuse, seul un partenrie ou un partenaire_admin peuvent effectuer cette action'
                ];

                return new JsonResponse($data, 403);
            }
            $users = $this->getUser()->getPartenaire();

            $utilisateur->setPartenaire($users);
            $utilisateur->setRoles(['ROLE_USER']);
        }


        $utilisateur->setStatut( $this->constante);
        $utilisateur->setImageFile($file);
        $utilisateur->setcreatedAt(new \Datetime);
        $utilisateur->setUpdatedAt(new \Datetime);
        $utilisateur->setCompte($form->get('compte')->getData());


        $errors = $validator->validate($utilisateur);

        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                 $this->content_type => $this->app_json
            ]);
        }

        $entityManager->persist($utilisateur);
        $entityManager->flush();



        $data = [
            $this->statut => 201,
            $this->message => 'L\'utilisateur a été créé'
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
        $partStat = $utilisateur->getPartenaire()->getStatut();
        
        if (!$utilisateur) {
            throw $this->createNotFoundException(
                'pas d\'utilisateur trouve pour cet id ' . $id
            );
        }

        $parts = $utilisateur->getPartenaire();
        if ($partStat == "actif") {


            $parts->setStatut( $this->bloquer);
            $utilisateur->setStatut( $this->bloquer);
        } else {

            $parts->setStatut( $this->constante);
            $utilisateur->setStatut( $this->constante);
        }
        $entityManager->flush();

        $data = [
            $this->statut => 200,
            $this->message => 'Le statut a ete bien mis a jour'
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/modif_partuser/{id}" , name="modif_partuser" , methods={"GET"})
     */
    public function updatePartuser($id)
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);
       

        if ($utilisateur->getStatut() == "actif") {


            $utilisateur->setStatut( $this->bloquer); 
            
        } else {

            $utilisateur->setStatut( $this->constante);
        }
        $entityManager->flush();

        $data = [
            $this->statut => 200,
            $this->message => 'Le statut a ete bien mis a jour'
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
        EntityManagerInterface $entityManager,
        PartenaireRepository $repository
    ) {
        #################recupération de l'ID du Partenaire####################

        $values = $request->request->all();
        $partenaire= new Partenaire();
        $form= $this->createForm(PartenaireType::class,$partenaire);
        $form->submit($values);

        
        $repository = $this->getDoctrine()->getRepository(Partenaire::class);

        $partenaire = $repository->findOneBy(['ninea' => $partenaire->getNinea()]);
        $user = $this->getUser();

        if ($partenaire) {
            $compte = new Compte();
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
                $this->statut => 201,
                'mes' => 'Le nouveau compte a ete cree par ' . $user->getNomcomplet()
            ];
            return new JsonResponse($data);
        } else {
            $data = [
                $this->statut => 500,
                'mes' => 'ce partenaire n\'existe pas'
            ];
            return new JsonResponse($data);
        }
    }
    /**
     * @Route("/listePartblock", name="listePartblock", methods={"GET"})
     * IsGranted("ROLE_SUPER_ADMIN")
     */
    public function listePartblock(PartenaireRepository $parte,SerializerInterface $serializer){
        
        $partenair = $parte->findBy(['statut'=> $this->bloquer]);
          

        $data = $serializer->serialize($partenair, 'json', [
           $this->groups => ['liste']
        ]);
      

        return new Response($data, 200, [
             $this->content_type => $this->app_json
        ]);
    }
     /**
     * @Route("/listePart", name="listePart", methods={"GET"})
     * IsGranted("ROLE_SUPER_ADMIN")
     */
    public function listePart(PartenaireRepository $parte,SerializerInterface $serializer){
        
        $partenair = $parte->findAll();
          

        $data = $serializer->serialize($partenair, 'json', [
           $this->groups => ['liste']
        ]);
      

        return new Response($data, 200, [
             $this->content_type => $this->app_json
        ]);
    }

    /**
     * @Route("/listeusers", name="listeusers", methods={"GET"})
     */

    public function listerusers(EntityManagerInterface $entityManager,
    SerializerInterface $serializer){

        $entityManager = $this->getDoctrine()->getManager();
        $utilisateurs = $entityManager->getRepository(Utilisateur::class)->findByRoles();
          

        $data = $serializer->serialize($utilisateurs, 'json', [
           $this->groups => [ $this->listeUtil]
        ]);
      

        return new Response($data, 200, [
             $this->content_type => $this->app_json
            
        ]);
    }

     /**
     * @Route("/Partusers", name="PartUsers", methods={"GET"})
     */

    public function PartUtil(EntityManagerInterface $entityManager,
    SerializerInterface $serializer){
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateurs = $entityManager->getRepository(Utilisateur::class)->findUsers($user);
            
        $data = $serializer->serialize($utilisateurs, 'json', [
           $this->groups => [ $this->listeUtil]
        ]);
      
        return new Response($data, 200, [
             $this->content_type => $this->app_json
            
        ]);
 

    }

    /**
     * @Route("/listePartenaires", name="listePartenaires", methods={"GET"})
     */

    public function listePartenaires(UtilisateurRepository $util,EntityManagerInterface $entityManager,
    SerializerInterface $serializer){

        $entityManager = $this->getDoctrine()->getManager();
        $utilisateurs = $entityManager->getRepository(Utilisateur::class)->findPartenaire();
          

        $data = $serializer->serialize($utilisateurs, 'json', [
           $this->groups => [ $this->listeUtil]
        ]);
      

        return new Response($data, 200, [
             $this->content_type => $this->app_json
            
        ]);
    }

    /**
     * @Route("/selecProfile", name="selecProfile", methods={"GET"})
     */

    public function selectProfile(ProfilRepository $profile,SerializerInterface $serializer){
        
        $profiles = $profile->findAll();
          

        $data = $serializer->serialize($profiles, 'json', [
           $this->groups => ['listeProfile']
        ]);
      

        return new Response($data, 200, [
             $this->content_type => $this->app_json
        ]);
    }


     /**
     * @Route("/selectCompte", name="selectCompte", methods={"GET"})
     */

     public function selectCompte(EntityManagerInterface $entityManager,
     SerializerInterface $serializer){
        $user=$this->getUser();
        
        if($user->getRoles()[0]!=$this->rolePart && $user->getRoles()[0]!=$this->rolePartAdmin){
           
            $data = [
                $this->statut => 403,
                $this->message => ' vous avez pas acces a ce contenu ' . $user->getNomcomplet()
            ];
            return new JsonResponse($data);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($user);
        $cpt = $utilisateur->getPartenaire()->getComptes();
        $data = $serializer->serialize($cpt, 'json', [
           $this->groups => ['selectCompte']
        ]);
      

        return new Response($data, 200, [
             $this->content_type => $this->app_json
        ]);
     } 
     
     /**
     * @Route("/findNinea", name="findNinea", methods={"POST"})
     */

     public function findNinea(Request $request,EntityManagerInterface $entityManager,
     SerializerInterface $serializer){
        $values = json_decode($request->getContent());
        $repo = $this->getDoctrine()->getRepository(Partenaire::class);
        $part = $repo->findOneBy(['ninea' => $values->ninea]);
        $data = $serializer->serialize($part, 'json', [
           $this->groups => ['mkCptList']
        ]);
      

        return new Response($data, 200, [
             $this->content_type => $this->app_json
        ]);
     }

  /**
     * @Route("/onepart/{id}", name="onepart", methods={"GET"})
     * IsGranted("ROLE_SUPER_ADMIN")
     */
    public function onepart(UtilisateurRepository $parte,SerializerInterface $serializer,$id=null){
        
        $partenair = $parte->findOneBy(['id'=>$id]);
          

        $data = $serializer->serialize($partenair, 'json', [
           $this->groups => [ $this->listeUtil]
        ]);
      

        return new Response($data, 200, [
             $this->content_type => $this->app_json
        ]);
    }

}
