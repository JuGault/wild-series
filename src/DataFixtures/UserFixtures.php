<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
     }
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i<=10; $i++) {
            $faker  =  Faker\Factory::create('fr_FR');
            $subscriber = new User();
            $subscriber->setEmail($faker->email);
            $subscriber->setRoles(['ROLE_SUBSCRIBER']);
            $subscriber->setPassword($this->passwordEncoder->encodePassword(
                $subscriber,
                'lol'
            ));
            $manager->persist($subscriber);
        }
        $admin = new User();
        $admin->setEmail('jugault45@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'taupegun'
        ));

        $manager->persist($admin);

        $manager->flush();
    }
}
