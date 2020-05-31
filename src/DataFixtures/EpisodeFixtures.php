<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface

{
    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }


    public function load(ObjectManager $manager)
    {
        $slugify = new Slugify();
        for ($i=0; $i<=50; $i++){
            $faker  =  Faker\Factory::create('fr_FR');
            $episode = new Episode();
            $episode->setTitle($faker->word);
            $episode->setNumber($faker->numberBetween(1, 10));
            $episode->setSeason($this->getReference('season_' . rand(0, 50)));
            $episode->setSynopsis($faker->text);
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $manager->persist($episode);
        }
        $manager->flush();
    }
}