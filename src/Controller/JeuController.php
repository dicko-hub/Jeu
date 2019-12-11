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
use App\Exception\DeadException;
use App\Repository\PersonnageRepository;
use App\Repository\SalleRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use App\Exception\NotSameRoomException;
use App\Exception\RessourceNotFound;
use App\Exception\WallException;
use App\Exception\SelfDestruction;

class JeuController extends Controller
{
    //cette fonction creer un joueur et le retourne 
    public function nouveauJoueur(){
        $manager = $this->getDoctrine()->getManager();
        //je recupere la premiere salle
        $SalleRepository = $this->getDoctrine()->getRepository(Salle::class);
        $salle = $SalleRepository->find(1);
        $joueur=new Personnage();
        //recuperation de l'id du dernier joueur pour allouer son incrementation a l'attribut guid du nouveau pour 
        //garder une concordence entre les attribut id et guid d'un meme joueur
        $PersonnageRepository = $this->getDoctrine()->getRepository(Personnage::class);
        $joueurs=$PersonnageRepository->findBy(array(), array('id' => 'desc'),1,0);
        $guid=$joueurs[0]->getId()+1;

        $joueur->setGuid($guid)
                ->setDegats(10)
                ->setVie(100)
                ->setDescription("vous etes un joueur, le numero $guid")
                ->setTotalVie(100)
                ->setSalle($salle)
                ->setType("Joueur");

        //persister le nouveau joueur dans la base
        $manager->persist($joueur);
        $manager->flush();
        return $joueur;
    }

    public function modelerJoueur($joueur){
        $salle=$joueur->getSalle();
        $passages=[];
        $entites=[];
        //on cree un tableau qui contient les passages de ma salle du joueur
        foreach ($joueur->getSalle()->getPassages() as $value)
        {
            array_push($passages,$value);
        }
        //on cree un tableau qui contient les joueurs dans la meme salle que le joueur
        foreach ($salle->getPersonnages() as $key=>$value)
        {
          array_push($entites,$value->getGuid());
        }
        //on construit le meme d'affichage dans le protocole
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
    public function modelerSalle($joueur){
            $salle=$joueur->getSalle();
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

    public function examinerJoueur($cible){
            $retour=["description"=>$cible->getDescription(),
                "type"=>$cible->getType(),
                "vie"=>$cible->getVie(),
                "totalvie"=>$cible->getTotalVie(),
             ];
            return $retour;
        }

    public function ImpactTaper($joueurCible,$guid){
        if($joueurCible=$guid){
            $message='{"type": "SelfDestruction", "message": "Vous vous attaquez vous meme (cest pas gentil pour les autres)"}';
            throw new SelfDestruction($message);
        }
        $manager = $this->getDoctrine()->getManager();
        $vieActuelle=$joueurCible->getVie()- $guid->getDegats();
        $degatsActuelle=$guid->getDegats()+5;
        $joueurCible->setVie($vieActuelle);
        $guid->setDegats($degatsActuelle);
        $manager->persist($joueurCible,$guid);
        $manager->flush();
        }

    public function executionDeplacement($guid,$data){

        $manager = $this->getDoctrine()->getManager();
        if(empty($data) || !is_string((json_decode($data))->{'direction'}))
            throw new RessourceNotFound("information json non valide");

        $result= json_decode($data);
        $direction = $result->{'direction'};
        $direction=strtoupper($direction);

        $SalleRepository = $this->getDoctrine()->getRepository(Salle::class);
        $salle = $SalleRepository->find($guid->getSalle());
        $trouver=false;
        foreach ($salle->getPassages() as $key => $value)
        {
            if($direction==$value)
            {
                        $salleNext = $SalleRepository->find($key);
                        $guid->setSalle($salleNext);
                $manager->persist($guid);
                $manager->flush();
                $trouver=true;
            }
        }
        if(!$trouver){
            //lieu ou declanchez l'exception si la direction n'existe pas
            $message='{"type": "MUR", "message": "Vous avez pris un mur"}';
            throw new WallException($message);
           
        }
        }

    public function memeSalle($guid,$cible){
        $SalleRepository = $this->getDoctrine()->getRepository(Salle::class);

        $salleGuid = $SalleRepository->find($guid->getSalle());
        $salleCible = $SalleRepository->find($cible->getSalle());
        if($salleGuid->getId()!=$salleCible->getId()){
            //lieu ou lever l'exception si les deux personnages ne sont pas dans la meme salle
            $message='{"type": "DIFFSALLE", "message": "Vous nêtes pas dans la même salle"}';
            throw new NotSameRoomException($message);
        }
    }
    
    public function verifeCible($data){
        $PersonnageRepository=$this->getDoctrine()->getRepository(Personnage::class);
        if(empty($data) || !is_int((json_decode($data))->{'cible'}))
            throw new RessourceNotFound("information json non valide");
        $result= json_decode($data);
        $cible = $result->{'cible'};
        $joueurCible=$PersonnageRepository->find($cible);
        if(!$joueurCible instanceof Personnage){
            //lieu ou lever l'exception si l'identifiant du joueur  n'existe pas
            throw new RessourceNotFound("votre cible  n'existe pas ");
        }
        return $joueurCible;

    }

    public function estEnVie($joueur){
        if($joueur->getVie()<=0)
        {
            $message='{"type": "MORT", "message": "Joueur mort (pas de bol)"}';
        throw new DeadException($message);
        }
    }

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
    public function creerJoueur(ObjectManager $manager)
    {
        //creer un joueur
        $joueur= self::nouveauJoueur();

        //code de sortie vers le client
        return self::modelerJoueur($joueur);
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
        //verifie si le joueur est en vie sinon DeadException
        self::estEnVie($guid);
        //code de sortie vers le client
        return self::modelerSalle($guid);
        
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
            //verifie si le joueur est en vie sinon DeadException
            self::estEnVie($guid);

            $data = $request->getContent();
            //lieu ou lever l'exception si aucune direction n'est reçu dans le corps du post ou invalide
            self::executionDeplacement($guid,$data);
        //code de sortir vers le client
        return self::modelerSalle($guid);
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
        //verifie si le joueur est en vie sinon DeadException
        self::estEnVie($guid);

        //verifie si ils sont dans la meme salle, sinon il aura une exception
        self::memeSalle($guid,$cible);

       //code de sortie vers le client
       
        return self::examinerJoueur($cible);
    }

     /**
     * @Post(
     *     path = "/{guid}/taper",
     *     name = "jeu_joueur_taper",
     *     requirements = {"guid"="\d+"}
     * )
     * @Rest\View(serializerGroups={"joueur"})
     */
    public function taper(Personnage $guid,Request $request,ObjectManager $manager)
    {
        //verifie si le joueur est en vie sinon DeadException
        self::estEnVie($guid);

        $data = $request->getContent();
           //verifie si la donnee envoyer en json est correct si la cible existe sinon exeption
           $joueurCible=self::verifeCible($data);

           //verifie si le joueur est en vie sinon DeadException
        self::estEnVie($joueurCible);
        //verifie si les deux joueur sont dans la meme salle
        self::memeSalle($guid,$joueurCible);
       //code effectuant les dommage de l'attaque sur la cible
       self::ImpactTaper($joueurCible,$guid);
       //code de sortie vers le client
        return self::examinerJoueur($joueurCible);
    }

  
}
