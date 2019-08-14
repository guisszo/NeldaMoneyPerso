<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TarifsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class TransactionController extends AbstractController
{
    /**
     * @Route("/transaction", name="transaction")
     */
    public function index()
    {
        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    #####################################################################################
    ########################### creation de la fonction d'envoi #########################


    /**
     * @Route("/envoi",name="envoi",methods={"POST"})
     */
    public function envoi(TarifsRepository $repo,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ){
        
        $values = $request->request->all();
    ###############code qui va générer le code transaction############

            $min = 1000;
            $max = 9999;
            $date       = new \DateTime;
            $dateformat   = $date->format('yd-Hs');
            $compte_rand = rand($min, $max).'-' . $dateformat;
    ###################################################################

        $transaction = new Transaction();
        $user =$this->getUser();
       
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->submit($values);
       

        
        $transaction->setUserEnvoi($user);
        $transaction->setCodeTransaction($compte_rand);
        $transaction->setStatut('disponible');
        $transaction->setSentAt(new \DateTime);
        $valeur=$transaction->getMontant();
        $value=$repo->findtarif($valeur);
        //var_dump($valeur);die();
        $transaction->setCommissionEnv($value[0]->getValeur()*0.1);
        $transaction->setCommissionEtat($value[0]->getValeur()*0.3);
        $transaction->setCommissionNeldam($value[0]->getValeur()*0.4);
        $cpt=$user->getCompte();
        $modsolde=$user->getCompte()->getSolde() - $transaction->getMontant()+$transaction->getCommissionEnv();
      $cpt->setSolde($modsolde);

       

        $errors = $validator->validate($transaction);

        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($cpt);
        $entityManager->persist($transaction);
        $entityManager->flush();





        $data = [
            'status' => 201,
            'msge' => 'L envoi a ete fait '
        ];

        return new JsonResponse($data, 201);
       
       
    }
}
