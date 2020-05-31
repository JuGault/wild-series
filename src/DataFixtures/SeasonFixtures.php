<?php
namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface

{
    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }


    public function load(ObjectManager $manager)
    {
        for ($i=0; $i<=50; $i++){
            $faker  =  Faker\Factory::create('fr_FR');
            $season = new Season();
            $season->setNumber($faker->numberBetween(1, 20));
            $season->setYear($faker->year);
            $season->setDescription($faker->text);
            $season->setProgram($this->getReference('program_' . rand(0, 5)));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }
        $manager->flush();
    }
}