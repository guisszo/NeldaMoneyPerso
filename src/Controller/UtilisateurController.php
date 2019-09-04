<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Form\CompteType;
use App\Form\DepotType;
use App\Repository\CompteRepository;
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

    /************************ Authentification ************************/


    /**
     *  @Route("/login", name="login", methods={"POST"})
     * @param Request $request
     * @param JWTEncoderInterface $JWTEncoder
     * @return JsonResponse
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function token(Request $request, JWTEncoderInterface $JWTEncoder)
    {
        $values = json_decode($request->getContent());

        $username = $values->username;
        $mdp = $values->password;

        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy([
            'username' => $username
        ]);


        if (!$user) {

            $data = [
                'sta' => 401,
                'me' => 'cette utilisateur n existe pas'
            ];
            return new JsonResponse($data);
        }


        $isValid = $this->encoder->isPasswordValid($user, $mdp);
        if (!$isValid) {
            $data = [
                'sta' => 400,
                'mes' => 'Votre mot de pass est incorrect'
            ];
            return new JsonResponse($data);
        }


        if (
            $user->getRoles() == ["ROLE_USER"] &&
            $user->getPartenaire()->getStatut() === "bloque" ||
            $user->getRoles() == ["ROLE_PARTENAIRE_ADMIN"] &&
            $user->getPartenaire()->getStatut() === "bloque"
        ) {

            $data = [
                'sta' => 401,
                'mes' => 'desole ' . $user->getNomcomplet() . ', vous avez pas acces aux services car ' . $user->getPartenaire()->getRaisonSociale() . 'est bloque'
            ];
            return new JsonResponse($data);
        }


        if (
            $user->getRoles() == ["ROLE_USER"] &&
            $user->getStatut() === "bloque" ||
            $user->getRoles() == ["ROLE_PARTENAIRE_ADMIN"] &&
            $user->getStatut() === "bloque"
        ) {
            $data = [
                'sta' => 401,
                'mes2' => 'acces refuse ' . $user->getNomcomplet() . ', vous etes bloque par ' . $user->getPartenaire()->getRaisonSociale()
            ];
            return new JsonResponse($data);
        }

        if ($user->getStatut() === "bloque") {


            $data = [
                'sta' => 401,
                'mes' => 'Desole ' . $user->getNomcomplet() . ', vous etes bloque, contacter l\' admin'
            ];
            return new JsonResponse($data);
        }

        $token = $JWTEncoder->encode([
            'username' => $user->getUsername(),
            'role' => $user->getRoles()[0],
            'exp' => time() + 7200 // 2 heures de validité
        ]);

        return new JsonResponse(['token' => $token]);
    }

    // /*************** Depot par le caissier *****************/

    /**
     * @Route("/depot", name="add_depot", methods={"POST"})
     *@IsGranted({"ROLE_CAISSIER"})
     */

    public function Depot(Request $request, SerializerInterface $serializer,ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $user=$this->getUser();
       $values = json_decode($request->getContent());
           // $values = $request->request->all();
       //     $depot = new Depot();
        //    $forme = $this->createForm(DepotType::class,$depot);
       // $forme->submit($values);

         //   $depot->getMontant();
               

                  //  $compte = new Compte();
                    $repo = $this->getDoctrine()->getRepository(Compte::class);
                    $compte = $repo->findOneBy(['numcompte' => $values->numcompte]);
    
                    $solde = $compte->getSolde();
                    $compte->setSolde($values->montant+ $solde);
                    $entityManager->persist($compte);
                    $entityManager->flush();
                    
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
             
                $data = [
                    'sttus' => 401,
                    'mesge' => 'le montant doit etre supérieur a 75000 FCFA'
                ];
                return new JsonResponse($data, 401);
            
        

            
       
        }
    

    /****************************************************************************/


    /**
     * @Route("/findCompte", name="findCompte",methods={"POST"})
     * IsGranted("ROLE_CAISSIER")
     */
    public function getCompt(
        CompteRepository $compte,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $data = $request->request->all();

        $compte = new Compte();
        $forme = $this->createForm(CompteType::class, $compte);

        $forme->submit($data);
        if ($forme->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $numcompte = $entityManager->getRepository(Compte::class)->findOneBy(['numcompte' => $compte->getNumcompte()]);
            $data = $serializer->serialize($numcompte, 'json', [
                'groups' => ['getcompte']
            ]);


            return new Response($data,200,[
                'Content-Type' => 'application/json'
            ]);
        }
    }
}
