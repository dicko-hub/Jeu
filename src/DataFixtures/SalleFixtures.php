<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Personnage;
use App\Entity\Salle;

class SalleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i=1;$i<7;$i++){

            $salle=new Salle();
            switch ($i) {
                case 1:
                    $salle->setDescription("Ceux-ci est la $i et vous ne pouvez ni allez a l'est, ni au sud ")
                          ->setPassages([2=>"N",4=>"W"]);
                    
                        $monstre=new Personnage();
                        $monstre->setGuid($i)
                                ->setDegats(mt_rand(0,100))
                                ->setVie(100)
                                ->setDescription("Ceux ci est l'un des monstres qui gardent la salle $i ")
                                ->setTotalVie(100)
                                ->setSalle($salle)
                                ->setType("Monstre");

                                $manager->persist($monstre);
    
                    break;
                case 2:
                    $salle->setDescription("Ceux-ci est la $i et vous ne pouvez pas allez a l'est")
                          ->setPassages([3=>"N",5=>"W",1=>"S"]);

                          $monstre=new Personnage();
                          $monstre->setGuid($i)
                                  ->setDegats(mt_rand(0,100))
                                  ->setVie(100)
                                  ->setDescription("Ceux ci est l'un des monstres qui gardent la salle $i ")
                                  ->setTotalVie(100)
                                  ->setSalle($salle)
                                  ->setType("Monstre");
  
                                  $manager->persist($monstre);
                    break;
                case 3:
                    $salle->setDescription("Ceux-ci est la $i et vous ne pouvez ni allez a l'est, ni au nord")
                          ->setPassages([2=>"S",6=>"W"]);

                          $monstre=new Personnage();
                        $monstre->setGuid($i)
                                ->setDegats(mt_rand(0,100))
                                ->setVie(100)
                                ->setDescription("Ceux ci est l'un des monstres qui gardent la salle $i ")
                                ->setTotalVie(100)
                                ->setSalle($salle)
                                ->setType("Monstre");

                                $manager->persist($monstre);
                    break;
                case 4:
                    $salle->setDescription("Ceux-ci est la $i et vous ne pouvez ni allez a l'ouest, ni au sud ")
                          ->setPassages([5=>"N",1=>"E"]);

                    $monstre=new Personnage();
                        $monstre->setGuid($i)
                                ->setDegats(mt_rand(0,100))
                                ->setVie(100)
                                ->setDescription("Ceux ci est l'un des monstres qui gardent la salle $i ")
                                ->setTotalVie(100)
                                ->setSalle($salle)
                                ->setType("Monstre");

                                $manager->persist($monstre);
                    break;
                case 5:
                    $salle->setDescription("Ceux-ci est la $i et vous ne pouvez pas allez a l'ouest")
                          ->setPassages([2=>"E",6=>"N",4=>"S"]);

                          $monstre=new Personnage();
                        $monstre->setGuid($i)
                                ->setDegats(mt_rand(0,100))
                                ->setVie(100)
                                ->setDescription("Ceux ci est l'un des monstres qui gardent la salle $i ")
                                ->setTotalVie(100)
                                ->setSalle($salle)
                                ->setType("Monstre");

                                $manager->persist($monstre);
                    break;
                case 6:

                    $salle->setDescription("Ceux-ci est la $i et vous ne pouvez ni allez a l'ouest, ni au nord ")
                          ->setPassages([3=>"E",5=>"S"]);

                    $monstre=new Personnage();
                        $monstre->setGuid($i)
                                ->setDegats(mt_rand(0,100))
                                ->setVie(100)
                                ->setDescription("Ceux ci est l'un des monstres qui gardent la salle $i ")
                                ->setTotalVie(100)
                                ->setSalle($salle)
                                ->setType("Monstre");

                                $manager->persist($monstre);
                    break;
                       
            }
            $manager->persist($salle);
        }
        $manager->flush();
    }
}
