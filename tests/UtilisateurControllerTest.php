<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UtilisateurControllerTest extends WebTestCase
{

    // public function testAjoutOk()
    // {
    //     $client = static::createClient([],[
    //         'PHP_AUTH_USER' => 'guisszo',
    //         'PHP_AUTH_PW' => 'pass',

    //     ]);
    //     $crawler = $client->request('POST', '/api/regpart',[],[],
    //     ['CONTENT_TYPE'=>"application/json"],
    //     '{
    //         "raison_sociale":"toucher SARL",
    //         "ninea":"1147996",
    //         "username": "Ndialaxane",
    //         "password": "pass",
    //         "adresse": "diamaguene",
    //         "tel":	"777634798",
    //         "email":	"fatou@gmail.com",
    //         "nomcomplet": "fatou fall",
    //         "statut":	"actif",
    //         "roles":["ROLE_ADMIN"]
        
    //     }');
    //     $rep=$client->getResponse();
    //     var_dump($rep);
    //     $this->assertSame(201,$client->getResponse()->getStatusCode());
    // }


    // public function testAjoutKo()
    // {
    //     $client = static::createClient([],[
    //         'PHP_AUTH_USER' => 'guisszo',
    //         'PHP_AUTH_PW' => 'pass',

    //     ]);
    //     $crawler = $client->request('POST', '/api/regpart',[],[],
    //     ['CONTENT_TYPE'=>"application/json"],
    //     '{
    //         "raison_sociale":"599999",
    //         "ninea":"1147996",
    //         "username": "ggggg",
    //         "password": "",
    //         "adresse": "123",
    //         "tel":	"bbbb",
    //         "email":	"12553",
          
    //         "statut":	"actif",
    //         "roles":[""]
        
    //     }');
    //     $rep=$client->getResponse();
    //     var_dump($rep);
    //     $this->assertSame(500,$client->getResponse()->getStatusCode());
    // }


    // public function testAjoutKoko()
    // {
    //     $client = static::createClient([],[
    //         'PHP_AUTH_USER' => 'guisszo',
    //         'PHP_AUTH_PW' => 'pass',

    //     ]);
    //     $crawler = $client->request('POST', '/api/regpart',[],[],
    //     ['CONTENT_TYPE'=>"application/json"],
    //     '{
    //         "raison_sociale":"599999",
    //         "ninea":"1147996",
    //         "username": "ggggg",
    //         "password": "",
    //         "adresse": "123",
    //         "tel":	"bbbb",
    //         "email":	"12553",
    //         "nomcomplet": "fatou fall",
    //         "statut":	"actif",
    //         "roles":[""]
        
    //     }');
    //     $rep=$client->getResponse();
    //     var_dump($rep);
    //     $this->assertSame(500,$client->getResponse()->getStatusCode());
    // }
    // public function testAjoutKoo()
    // {
    //     $client = static::createClient([],[
    //         'PHP_AUTH_USER' => 'guisszo',
    //         'PHP_AUTH_PW' => 'pass',

    //     ]);
    //     $crawler = $client->request('POST', '/api/regpart',[],[],
    //     ['CONTENT_TYPE'=>"application/json"],
    //     '{
    //         "raison_sociale":"599999",
    //         "ninea":"1147996",
    //         "username": "",
    //         "password": "",
    //         "adresse": "123",
    //         "tel":	"bbbb",
    //         "email":	"12553",
    //         "nomcomplet": "fatou fall",
    //         "statut":	"actif",
    //         "roles":[""]
        
    //     }');
    //     $rep=$client->getResponse();
    //     var_dump($rep);
    //     $this->assertSame(500,$client->getResponse()->getStatusCode());
    // }


    //  public function testDepotok()
    // {
    //     $client = static::createClient([],[
    //         'PHP_AUTH_USER' => 'Caissier',
    //         'PHP_AUTH_PW' => 'pass',

    //     ]);
    //     $crawler = $client->request('POST', '/api/depot',[],[],
    //     ['CONTENT_TYPE'=>"application/json"],
    //     '{
    //         "numcompte":"1243619041936",
    //         "montant": "595000"
    //     }');
    //     $rep=$client->getResponse();
    //     var_dump($rep);
    //     $this->assertSame(200,$client->getResponse()->getStatusCode());
    // }

    // public function testDepotko()
    // {
    //     $client = static::createClient([],[
    //         'PHP_AUTH_USER' => 'Caissier',
    //         'PHP_AUTH_PW' => 'pass',

    //     ]);
    //     $crawler = $client->request('POST', '/api/depot',[],[],
    //     ['CONTENT_TYPE'=>"application/json"],
    //     '{
    //         "numcompte":1243619041936,
    //         "montant": ""
    //     }');
    //     $rep=$client->getResponse();
    //     var_dump($rep);
    //     $this->assertSame(500,$client->getResponse()->getStatusCode());
    // }
}
