<?php

namespace App\Controller;

use App\Form\RetraitType;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/api")
 */
class TransactionController extends AbstractController
{
    
    #####################################################################################
    ########################### creation de la fonction d'envoi #########################


    /**
     * @Route("/envoi",name="envoi",methods={"POST"})
     */
    public function envoi(
        TarifsRepository $repo,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $user = $this->getUser();
        $values = $request->request->all();
        ###############code qui va générer le code transaction############

        $min = 1000;
        $max = 9999;
        $date       = new \DateTime;
        $dateformat   = $date->format('yH-ds');
        $compte_rand = rand($min, $max) . '-' . $dateformat;
        ###################################################################

        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->submit($values);

        $transaction->setUserEnvoi($user);
        $transaction->setCodeTransaction($compte_rand);
        $transaction->setStatut('disponible');
        $transaction->setSentAt(new \DateTime);
        ################ on recupere le montant qui est envoyé ################
        $valeur = $transaction->getMontant();

        ############### et on donne ce montant à la fonction findtarif pour trouver
        ############### l'intervalle où se trouve les frais d'envoi
        
         if(2000001 <= $valeur && $valeur< 3000000){
            $value= $valeur *0.2;
        }else{
            $value = $repo->findtarif($valeur);
        }
       
        ######## calcul des commisions avec le tarif trouvé

        $transaction->setCommissionEnv($value[0]->getValeur() * 0.1);
        $transaction->setCommissionEtat($value[0]->getValeur() * 0.3);
        $transaction->setCommissionNeldam($value[0]->getValeur() * 0.4);

        $cpt = $user->getCompte(); ########### recupération du numero de compte
        $modsolde = $user->getCompte()->getSolde() -
            $transaction->getMontant() +
            $transaction->getCommissionEnv();
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
    #####################################################################################
    ######################## creation de la fonction de reception #######################
    /**
     * @Route("/retrait",name="retrait",methods={"POST"})
     */
    public function retrait(
        TarifsRepository $repos,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $user = $this->getUser();
        $values = $request->request->all();

        ###################################################################

        $transaction = new Transaction();
        $form = $this->createForm(RetraitType::class, $transaction);
        $form->submit($values);

        ############# comparaison du code avec celui généré #################
        $repo = $this->getDoctrine()->getRepository(Transaction::class);
        $codeGen = $repo->findOneBy(['codeTransaction' => $transaction->getCodeTransaction()]);

        if (!$codeGen) {
            throw new NotFoundHttpException('ce conde n\'existe pas ');
        }
        $codeGen->setUserRetrait($user);
        $codeGen->setRecevedAt(new \DateTime);
        $codeGen->setCNIrecepteur($values['CNIrecepteur']);
        $codeGen->setStatut('retire');
        ################ on recupere le montant qui est envoyé ################
        $valeur = $codeGen->getMontant();

        ############### et on donne ce montant à la fonction findtarif pour trouver
        ############### l'intervalle où se trouve les frais d'envoi
       
        if(2000001 <= $valeur && $valeur< 3000000){
            $value= $valeur *0.2;
        }else{
            $value = $repos->findtarif($valeur);
        }
        ######## calcul de la commission grace à la tarif trouvée

        $codeGen->setCommissionRetrait($value[0]->getValeur() * 0.2);


        $cpt = $user->getCompte(); ########### recupération du numero de compte
        $modsolde = $user->getCompte()->getSolde() +
        $codeGen->getMontant() +
        $codeGen->getCommissionRetrait();
        $cpt->setSolde($modsolde);



        $errors = $validator->validate($transaction);

        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json'
            ]);
        }
        $entityManager->persist($cpt);
        $entityManager->persist($codeGen);
        $entityManager->flush();

        $data = [
            'status' => 201,
            'msge' => 'Le retrait a ete fait '
        ];

        return new JsonResponse($data, 201);
    }
}
