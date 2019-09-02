<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Utilisateur;
use App\Form\CompteType;
use App\Form\DepotType;
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
    
public function Depot(Request $request,EntityManagerInterface $entityManager ): Response
{
    $depot = new Depot();
    $form = $this->createForm(DepotType::class,$depot);
    $data=$request->request->all();
    
    $depot->getMontant();
    $compte= new Compte();
    $forme=$this-> createForm(CompteType::class,$compte);
  
    $form->submit($data);
    $forme->submit($data);
    if($form->isSubmitted() && $forme->isSubmitted()){  
         $depot->getMontant();
         $compte->getSolde();
            if ($depot->getMontant()>=75000) {
                $repo = $this->getDoctrine()->getRepository(Compte::class);
                $numcompte = $repo->findOneBy(['numcompte' => $compte->getNumcompte()]);
            
                $user=$this->getUser();
                $depot->setCreatedAt(new \DateTime());
                $depot->setCompte($numcompte);
                $depot->setCaissier($user);
                $depot-> setMtnAvantDepot($numcompte->getSolde());
            $compte->setSolde($numcompte->getSolde()+$depot->getMontant());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($numcompte);
            $entityManager->persist($depot);
            $entityManager->flush();
        return new Response('Le dépôt a été effectué',Response::HTTP_CREATED);
        }
        return new Response('Le montant doit etre superieur ou egal a 75 000',Response::HTTP_CREATED);
    }

  $data = [
        'status' => 500,
        'message' => 'Vous devez renseigner le montant et le compte où doit être effectuer le dépot '
    ];
    return new Response($data, 500); 

}

    /****************************************************************************/
}
