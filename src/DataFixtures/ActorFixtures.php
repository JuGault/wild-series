<?php
namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ActorFixtures extends Fixture

{

    public function load(ObjectManager $manager)
    {
        $faker  =  Faker\Factory::create('en_US');
        $k = 0;
        for ($i=0; $i<=50; $i++){
            $actor = new actor();
            $actor->setName($faker->name);
            $this->addReference('actor_' . $k, $actor);
            $manager->persist($actor);
            $k++;

        }
        $manager->flush();
    }
}