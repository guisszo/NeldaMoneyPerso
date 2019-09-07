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
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->statut ='statut';
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
        $constante = 'actif';

        $values = $request->request->all();

        /********************** Insertion Partenaire ***********************/

        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->submit($values);
        $partenaire->setStatut($constante);
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
                $utilisateur->setStatut($constante);
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
                    $this->statut => 201,
                    'message' => 'Le partenaire a été créé'
                ];

                return new JsonResponse($data, 201);
            }
        }
    }


    /************** Insertion d'un utilisateur simple par super_admin********** */

    /**
     * @Route("/registeruser", name="registeruser", methods={"POST"})
     * IsGranted("ROLE_SUPER_ADMIN","ROLE_PARTENAIRE")
     */
    public function reguser(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $constante = 'actif';
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
                    'message' => 'acces refuse, vous etes pas autorise a faire cette action'
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
                    'message' => 'acces refuse, vous etes pas autorise a faire cette action'
                ];

                return new JsonResponse($data, 403);
            }
            $utilisateur->setRoles(['ROLE_CAISSIER']);
        } elseif ($profils->getLibelle() == "partenaire_admin") {

            if (
                $this->getUser()->getRoles()[0] !== "ROLE_PARTENAIRE" &&
                $this->getUser()->getRoles()[0] !== "ROLE_PARTENAIRE_ADMIN"
            ) {

                $data = [
                    $this->statut => 403,
                    'message' => 'acces refuse, seul un partenrie ou un partenaire_admin peuvent effectuer cette action'
                ];

                return new JsonResponse($data, 403);
            }
            $users = $this->getUser()->getPartenaire();

            $utilisateur->setPartenaire($users);
            $utilisateur->setRoles(['ROLE_PARTENAIRE_ADMIN']);
        } elseif ($profils->getLibelle() == "user") {

            if (
                $this->getUser()->getRoles()[0] != "ROLE_PARTENAIRE" &&
                $this->getUser()->getRoles()[0] != "ROLE_PARTENAIRE_ADMIN"
            ) {

                $data = [
                    $this->statut => 403,
                    'message' => 'acces refuse, seul un partenrie ou un partenaire_admin peuvent effectuer cette action'
                ];

                return new JsonResponse($data, 403);
            }
            $users = $this->getUser()->getPartenaire();

            $utilisateur->setPartenaire($users);
            $utilisateur->setRoles(['ROLE_USER']);
        }


        $utilisateur->setStatut($constante);
        $utilisateur->setImageFile($file);
        $utilisateur->setcreatedAt(new \Datetime);
        $utilisateur->setUpdatedAt(new \Datetime);
        $utilisateur->setCompte($form->get('compte')->getData());


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
            $this->statut => 201,
            'message' => 'L\'utilisateur a été créé'
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
        $constante = 'actif';
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


            $parts->setStatut('bloque');
            $utilisateur->setStatut('bloque');
        } else {

            $parts->setStatut($constante);
            $utilisateur->setStatut($constante);
        }
        $entityManager->flush();

        $data = [
            $this->statut => 200,
            'message' => 'Le statut a ete bien mis a jour'
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/modif_partuser/{id}" , name="modif_partuser" , methods={"GET"})
     */
    public function updatePartuser($id)
    {
        $constante = 'actif';
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($id);
       

        if ($utilisateur->getStatut() == "actif") {


            $utilisateur->setStatut('bloque'); 
            
        } else {

            $utilisateur->setStatut($constante);
        }
        $entityManager->flush();

        $data = [
            $this->statut => 200,
            'message' => 'Le statut a ete bien mis a jour'
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
        
        $partenair = $parte->findBy(['statut'=>'bloque']);
          

        $data = $serializer->serialize($partenair, 'json', [
            'groups' => ['liste']
        ]);
      

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
     /**
     * @Route("/listePart", name="listePart", methods={"GET"})
     * IsGranted("ROLE_SUPER_ADMIN")
     */
    public function listePart(PartenaireRepository $parte,SerializerInterface $serializer){
        
        $partenair = $parte->findAll();
          

        $data = $serializer->serialize($partenair, 'json', [
            'groups' => ['liste']
        ]);
      

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
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
            'groups' => ['listeutile']
        ]);
      

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
            
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
            'groups' => ['listeutile']
        ]);
      
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
            
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
            'groups' => ['listeutile']
        ]);
      

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
            
        ]);
    }

    /**
     * @Route("/selecProfile", name="selecProfile", methods={"GET"})
     */

    public function selectProfile(ProfilRepository $profile,SerializerInterface $serializer){
        
        $profiles = $profile->findAll();
          

        $data = $serializer->serialize($profiles, 'json', [
            'groups' => ['listeProfile']
        ]);
      

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }


     /**
     * @Route("/selectCompte", name="selectCompte", methods={"GET"})
     */

     public function selectCompte(EntityManagerInterface $entityManager,
     SerializerInterface $serializer){
        $user=$this->getUser();
        
        if($user->getRoles()[0]!="ROLE_PARTENAIRE" && $user->getRoles()[0]!="ROLE_PARTENAIRE_ADMIN"){
           
            $data = [
                $this->statut => 403,
                'mes' => ' vous avez pas acces a ce contenu ' . $user->getNomcomplet()
            ];
            return new JsonResponse($data);
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($user);
        $cpt = $utilisateur->getPartenaire()->getComptes();
        $data = $serializer->serialize($cpt, 'json', [
            'groups' => ['selectCompte']
        ]);
      

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
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
            'groups' => ['mkCptList']
        ]);
      

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
     }

  /**
     * @Route("/onepart/{id}", name="onepart", methods={"GET"})
     * IsGranted("ROLE_SUPER_ADMIN")
     */
    public function onepart(UtilisateurRepository $parte,SerializerInterface $serializer,$id=null){
        
        $partenair = $parte->findOneBy(['id'=>$id]);
          

        $data = $serializer->serialize($partenair, 'json', [
            'groups' => ['listeutile']
        ]);
      

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

}
