<?php

namespace App\Controller;
use Osms\Osms;
use App\Entity\Compte;
use App\Entity\Tarifs;
use App\Form\RetraitType;
use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TarifsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/api")
 */
class TransactionController extends AbstractController
{
    private $status;
    private $message;
    private $groups;
    private $content_type;
    private $app_json;
    private $dateFrom;
    private $dateTo;
    public function __construct()
    {
        $this->groups = 'groups';
        $this->status = 'status';
        $this->message = 'message';
        $this->content_type = 'Content-Type';
        $this->app_json = 'application/json';
        $this->dateFrom = 'debut';
        $this->dateTo = 'fin';
    }

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
        if ($user->getCompte()->getSolde() < $form->get('montant')->getData()) {
            $data = [
                $this->status => 208,
                $this->message => 'Votre solde ne vous permet pas de faire d\'envoi'
            ];

            return new JsonResponse($data, 208);
        }

        $transaction->setUserEnvoi($user);
        $transaction->setCodeTransaction($compte_rand);
        $transaction->setStatut('disponible');
        $transaction->setSentAt(new \DateTime);
        ################ on recupere le montant qui est envoyé ################
        $valeur = $transaction->getMontant();

        ############### et on donne ce montant à la fonction findtarif pour trouver
        ############### l'intervalle où se trouve les frais d'envoi

        if (2000001 <= $valeur && $valeur < 3000000) {
            $value = $valeur * 0.2;
        } else {
            $value = $repo->findtarif($valeur);
        }

        ######## calcul des commisions avec le tarif trouvé

        $transaction->setCommissionEnv($value[0]->getValeur() * 0.1);
        $transaction->setCommissionEtat($value[0]->getValeur() * 0.3);
        $transaction->setCommissionNeldam($value[0]->getValeur() * 0.4);

        ####### on met la commission Neldam sur le compte de neldam ici
        $repo = $this->getDoctrine()->getRepository(Compte::class);
        $superC = $repo->findOneBy(['id' =>  1]);
        $soldeSuper = $superC->getSolde();
        $superC->setSolde($soldeSuper + $transaction->getCommissionNeldam());
        #########" fin de cette etape

        $cpt = $user->getCompte(); ########### recupération du numero de compte
        $modsolde = $user->getCompte()->getSolde() -
            $transaction->getMontant() +
            $transaction->getCommissionEnv();
        $cpt->setSolde($modsolde);



        $errors = $validator->validate($transaction);

        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                $this->content_type => $this->app_json
            ]);
        }

        $entityManager->persist($superC);
        $entityManager->persist($cpt);
        $entityManager->persist($transaction);
        $entityManager->flush();
        
        $config = array();
        
        $osms = new Osms($config);
        
        // retrieve an access token
        $response = $osms->getTokenFromConsumerKey();
        
        if (!empty($response['access_token'])) {
            $senderAddress = 'tel:+221'.$form->get('telExpediteur')->getData();
            $receiverAddress = 'tel:+221'.$form->get('telRecepteur')->getData();
            $messag = 'Bonjour '.$form->get('nomcompletRecepteur')->getData().', vous avez recu '.$valeur
            .' de la part de '.$form->get('nomcompletExpediteur')->getData()
            .' tel:'.$form->get('telExpediteur')->getData()
            .'. Le code de retrait est: '.$transaction->getCodeTransaction()
            .'. Disponible dans tous les agences Neldam.'
            ;
            $senderName = 'send';
        
            $osms->sendSMS($senderAddress, $receiverAddress, $messag, $senderName);
        } else {
            // error
        }
        
        $data = $serializer->serialize($transaction, 'json', [
            $this->groups => ['transactionEnv']
        ]);

        return new Response($data, 201, [
            $this->content_type => $this->app_json
        ]);
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

        $codeGen->setUserRetrait($user);
        $codeGen->setRecevedAt(new \DateTime);
        $codeGen->setCNIrecepteur($values['CNIrecepteur']);
        $codeGen->setStatut('retire');
        ################ on recupere le montant qui est envoyé ################
        $valeur = $codeGen->getMontant();

        ############### et on donne ce montant à la fonction findtarif pour trouver
        ############### l'intervalle où se trouve les frais d'envoi

        if (2000001 <= $valeur && $valeur < 3000000) {
            $value = $valeur * 0.2;
        } else {
            $value = $repos->findtarif($valeur);
        }
        ######## calcul de la commission grace au trouvée

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
                $this->content_type => $this->app_json
            ]);
        }
        $entityManager->persist($cpt);
        $entityManager->persist($codeGen);
        $entityManager->flush();

        $data = [
            $this->status => 201,
            $this->message => 'Le retrait a ete fait '
        ];

        return new JsonResponse($data, 201);
    }
    ######################################################################
    ############# recherche du code de la transaction ####################

    /**
     * @Route("/findCode", name="findCode", methods={"POST"})
     */

    public function findCode(
        TransactionRepository $transaction,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $user = $this->getUser();
        $data = $request->request->all();

        $transaction = new Transaction();
        $forme = $this->createForm(RetraitType::class, $transaction);

        $forme->submit($data);
        if ($forme->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $Code = $entityManager->getRepository(Transaction::class)
                ->findOneBy(['codeTransaction' => $transaction->getCodeTransaction()]);

            if (!$Code) {
                $data = [
                    $this->status => 208,
                    $this->message => 'Ce code n\'existe pas '
                ];

                return new JsonResponse($data, 208);
            }
            if ($Code->getStatut() === 'retire') {
                $data = [
                    $this->status => 209,
                    $this->message => 'Le retrait a deja été effectué  pour ce code'
                ];

                return new JsonResponse($data, 209);
            }
            $data = $serializer->serialize($Code, 'json', [
                'userRetrait' =>  $Code->setUserRetrait($user),
                $this->groups => ['CodeTransaction']
            ]);

            return new Response($data, 200, [
                $this->content_type => $this->app_json
            ]);
        }
    }

     /**
     * @Route("/Trouvertarif", name="Trouvertarif", methods={"POST"})
     */
    public function trouverTarif(TarifsRepository $repo,
    Request $request,
    SerializerInterface $serializer
    )
    {
        $data = $request->request->all();

        $transaction = new Transaction();
        $forme = $this->createForm(TransactionType::class, $transaction);

        $forme->submit($data);
        if ($forme->isSubmitted()) {
           
            $valeur = $repo-221>findtarif($forme->get('montant')->getData());

            
            $data = $serializer->serialize($valeur, 'json', [
                'groups' => ['getvaleur']
            ]);
            return new Response($data, 200, [
                $this->content_type => $this->app_json
            ]);
            
        }
    }

        /**
     * @Route("/listeTransactionsEnv", name="Transactionsenv", methods={"GET"})
     * IsGranted("ROLE_PARTENAIRE","ROLE_PARTENAIRE_ADMIN","ROLE_USER")
     */
    public function TransactListEnv(EntityManagerInterface $entityManager,
    SerializerInterface $serializer){
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        $utilisateurs = $entityManager->getRepository(Transaction::class)->findTransactionsEnvoi($user);
        $data = $serializer->serialize($utilisateurs, 'json', [
           $this->groups => ['envlistTransact']
        ]);
      
        return new Response($data, 200, [
             $this->content_type => $this->app_json
            
        ]);
 

    }

    /**
     * @Route("/listeTransactionsRetrait", name="Transactionsretrait", methods={"GET"})
     * IsGranted("ROLE_PARTENAIRE","ROLE_PARTENAIRE_ADMIN","ROLE_USER")
     */
    public function TransactListRetrait(EntityManagerInterface $entityManager,
    SerializerInterface $serializer){
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        $utilisateurs = $entityManager->getRepository(Transaction::class)->findTransactionsretrait($user);
        $data = $serializer->serialize($utilisateurs, 'json', [
           $this->groups => ['retraitlistTransact']
        ]);
      
        return new Response($data, 200, [
             $this->content_type => $this->app_json
            
        ]);
 

    }

    /**
     * @Route("/RechercheDateEnv", name="RechercheEnvoi", methods={"GET","POST"})
     * IsGranted("ROLE_PARTENAIRE","ROLE_PARTENAIRE_ADMIN","ROLE_USER")
     */

     public function RechercheEnv(SerializerInterface $serializer,
     Request $request,TransactionRepository $repo){
       
        $user = $this->getUser();
        $values = json_decode($request->getContent(),true);
        if (!$values) {
            $values = $request->request->all();
        }
        
        $de = new \DateTime($values[$this->dateFrom]);
        $de_format=$de->format('Y-m-d')."00-00-00";
        $a = new \DateTime($values[$this->dateTo]);
        $a_format =$a->format('Y-m-d')."23-59-59";
        $rechercheDate = $repo->RechercheDateE($de_format,$a_format,$user);
        
        
        if($rechercheDate==[]){
            $data = [
                $this->status => 404,
                $this->message => 'Il n y a pas de transactions pour ces dates'
            ];
            return new JsonResponse($data,404);
        }
        
        $data = $serializer->serialize($rechercheDate, 'json', [
           $this->groups => ['envlistTransact']
        ]);
       
        return new Response($data, 200, [
             $this->content_type => $this->app_json
            
        ]);
     }

     /**
     * @Route("/RechercheDateRetrait", name="RechercheRetrait", methods={"GET","POST"})
     * IsGranted("ROLE_PARTENAIRE","ROLE_PARTENAIRE_ADMIN","ROLE_USER")
     */

    public function RechercheRetrait(SerializerInterface $serializer,
    Request $request,TransactionRepository $repo){
      
       $user = $this->getUser();
       $values = json_decode($request->getContent(),true);
       if (!$values) {
           $values = $request->request->all();
       }
       
       $de = new \DateTime($values[$this->dateFrom]);
       $de_format=$de->format('Y-m-d')."00-00-00";
       $a = new \DateTime($values[$this->dateTo]);
       $a_format =$a->format('Y-m-d')."23-59-59";
       $rechercheDate = $repo->RechercheDateR($de_format,$a_format,$user);

       if($rechercheDate==[]){
            $data = [
                $this->status => 404,
                $this->message => 'Il n y a pas de transactions pour ces dates'
            ];
            return new JsonResponse($data,404);
        }
       $data = $serializer->serialize($rechercheDate, 'json', [
          $this->groups => ['retraitlistTransact']
       ]);
      
       return new Response($data, 200, [
            $this->content_type => $this->app_json
           
       ]);
    }
}
