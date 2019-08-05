<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UtilisateurFixtures extends Fixture
{
    private $encoder;

public function __construct(UserPasswordEncoderInterface $encoder)
{
    $this->encoder = $encoder;
}
    public function load( ObjectManager $manager)
    {
        $utilisateur = new Utilisateur();
            $utilisateur->setUsername('guisszo');
            $password = $this->encoder->encodePassword($utilisateur, 'pass');
            $utilisateur->setPassword($password);
            $utilisateur->setRoles(array('ROLE_SUPER_ADMIN'));
            $utilisateur->setNomComplet('Boubacarla Guisse');
            $utilisateur->setTel(777274988);
            $utilisateur->setAdresse('Petit Mbao');
            $utilisateur->setStatut('actif');
            $utilisateur->setEmail('admin@administration.com');
            $utilisateur->setcreatedAt(new \Datetime);
            $utilisateur->setUpdatedAt(new \Datetime);

        $manager->persist($utilisateur);

        $manager->flush();
    }
}
