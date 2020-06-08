<?php
namespace App\DataFixtures;

use App\Entity\Actor;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ActorFixtures extends Fixture

{

    public function load(ObjectManager $manager)
    {
        $slugify = new Slugify();
        $faker  =  Faker\Factory::create('en_US');
        $k = 0;
        for ($i=0; $i<=50; $i++){
            $actor = new actor();
            $actor->setName($faker->name);
            $actor->setBirthday($faker->date('Y-m-d'));
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $this->addReference('actor_' . $k, $actor);
            $manager->persist($actor);
            $k++;

        }
        $manager->flush();
    }
}
