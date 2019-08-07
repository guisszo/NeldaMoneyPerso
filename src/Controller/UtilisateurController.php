<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
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
class UtilisateurController extends AbstractController
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
   
    /************************ Authentification ************************/

    
    /**
     *  @Route("/login_chec", name="login", methods={"POST"})
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
           
            $data = [
                'sta' => 400,
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
        if($user->getStatut()==="bloque" ){
           
           
            $data = [
                'sta' => 400,
                'mes' => 'acces refuse, vous etes bloque'
            ];
            return new JsonResponse($data);
        
        }
        $token = $JWTEncoder->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 7200 // 2 heures d'expiration
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
                    'sttus' => 500,
                    'mesge' => 'le montant doit etre supérieur a 75000 FCFA'
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

    /*****************************************************/

    
     
}
