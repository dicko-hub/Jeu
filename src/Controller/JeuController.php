<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Personnage;
use App\Entity\Salle;
use App\Repository\PersonnageRepository;
use App\Repository\SalleRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;

class JeuController extends Controller
{
    /**
     * @Route("/jeu", name="jeu")
     */
    public function index()
    {
        return $this->render('jeu/index.html.twig', [
            'controller_name' => 'JeuController',
        ]);
    }

      /**
     * @Rest\Post(
     *    path = "/connect",
     *    name = "jeu_joueur_creer"
     * )
     * @Rest\View(StatusCode = 201,serializerGroups={"connect_joueur"})
     */
    public function creerJoueur()
    {
        //je recupere la premiere salle
        $SalleRepository = $this->getDoctrine()->getRepository(Salle::class);
        $salle = $SalleRepository->find(1);
        $joueur=new Personnage();
        $PersonnageRepository = $this->getDoctrine()->getRepository(Personnage::class);
        $joueurs=$PersonnageRepository->findBy(array(), array('id' => 'desc'),1,0);
        $guid=$joueurs[0]->getId()+1;

        
        $joueur->setGuid($guid)
                ->setDegats(mt_rand(0,100))
                ->setVie(100)
                ->setDescription("vous etes dans la premiere salle ")
                ->setTotalVie(100)
                ->setSalle($salle)
                ->setType("Joueur");

        //code de sortie vers le client
        $passages=[];
        $entites=[];
        foreach ($joueur->getSalle()->getPassages() as $value)
        {
            array_push($passages,$value);
        }
        foreach ($salle->getPersonnages() as $key=>$value)
        {
          array_push($entites,$value->getGuid());
        }

        $retour=["guid"=>$joueur->getGuid(),
                 "totalVie"=>$joueur->getTotalVie(),
                 "salle"=>[
                     "description"=>$joueur->getSalle()->getDescription(),
                     "passages"=>$passages,
                     "entites"=>$entites
                 ]
    ];
        return $retour;
    }

     
    /**
     * @Get(
     *     path = "/{guid}/regarder",
     *     name = "jeu_joueur_regarder",
     *     requirements = {"guid"="\d+"}
     * )
     * @Rest\View(serializerGroups={"salle"})
     */
    public function regarder(Personnage $guid)
    {
        $SalleRepository = $this->getDoctrine()->getRepository(Salle::class);
        $salle = $SalleRepository->find($guid->getSalle());

        //code de sortir vers le client
        $passages=[];
        $entites=[];
        foreach ($salle->getPassages() as $value)
        {
            array_push($passages,$value);
        }
        foreach ($salle->getPersonnages() as $key=>$value)
        {
          array_push($entites,$value->getGuid());
        }

        $retour=[    "description"=>$salle->getDescription(),
                     "passages"=>$passages,
                     "entites"=>$entites
    ];
        return $retour;
    }

    /**
     * @Post(
     *     path = "/{guid}/deplacement",
     *     name = "jeu_joueur_deplacement",
     *     requirements = {"guid"="\d+"}
     * )
     * @Rest\View(serializerGroups={"salle"})
     */
    public function deplacement(Personnage $guid,Request $request,ObjectManager $manager)
    {
        $data = $request->getContent();
        $result= json_decode($data);
        $direction = $result->{'direction'};

        $SalleRepository = $this->getDoctrine()->getRepository(Salle::class);
        $salle = $SalleRepository->find($guid->getSalle());
        foreach ($salle->getPassages() as $key => $value)
        {
            if($direction==$value)
            {
                        $salleNext = $SalleRepository->find($key);
                        $guid->setSalle($salleNext);
                $manager->persist($guid);
                $manager->flush();
            }
        }
        $salleActuel=$SalleRepository->find($guid->getSalle());

        //code de sortir vers le client
        $passages=[];
        $entites=[];
        foreach ($salleActuel->getPassages() as $value)
        {
            array_push($passages,$value);
        }
        foreach ($salleActuel->getPersonnages() as $key=>$value)
        {
          array_push($entites,$value->getGuid());
        }

        $retour=[    "description"=>$salleActuel->getDescription(),
                     "passages"=>$passages,
                     "entites"=>$entites
    ];
        return $retour;
    }

     /**
     * @Get(
     *     path = "/{guid}/examiner/{cible}",
     *     name = "jeu_joueur_examiner",
     *     requirements = {"guid"="\d+","cible"="\d+"}
     * )
     * @Rest\View(serializerGroups={"joueur"})
     */
    public function examiner(Personnage $guid,Personnage $cible )
    {
        $SalleRepository = $this->getDoctrine()->getRepository(Salle::class);
        $salleGuid = $SalleRepository->find($guid->getSalle());
        $salleCible = $SalleRepository->find($cible->getSalle());
       // if($salleGuid->getId()==$salleCible->getId())

       //code de sortie vers le client
       $retour=["description"=>$cible->getDescription(),
                "type"=>$cible->getType(),
                "vie"=>$cible->getVie(),
                "totalvie"=>$cible->getTotalVie(),
   ];
        return $retour;
    }

     /**
     * @Post(
     *     path = "/{guid}/taper",
     *     name = "jeu_joueur_taper",
     *     requirements = {"guid"="\d+"}
     * )
     * @Rest\View(serializerGroups={"joueur"})
     */
    public function taper(Personnage $guid,Request $request,ObjectManager $manage)
    {
        $data = $request->getContent();
        $result= json_decode($data);
        $cible = $result->{'cible'};

        $PersonnageRepository= $this->getDoctrine()->getRepository(Personnage::class);
        $joueurCible=$PersonnageRepository->find($cible);

        $SalleRepository = $this->getDoctrine()->getRepository(Salle::class);
        $salleGuid = $SalleRepository->find($guid->getSalle());
        $salleCible = $SalleRepository->find($cible);
       // if($salleGuid->getId()==$salleCible->getId())

       
       //code de sortie vers le client
       $retour=["description"=>$joueurCible->getDescription(),
                "type"=>$joueurCible->getType(),
                "vie"=>$joueurCible->getVie(),
                "totalvie"=>$joueurCible->getTotalVie(),
   ];
        return $retour;
    }

  
}
