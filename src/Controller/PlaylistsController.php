<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 */
class PlaylistsController extends AbstractController {
    
    /**
     * 
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * constante qui mène à la page des playlists
     */
    private const RETOURNEPLAYLISTS = "pages/playlists.html.twig";

    
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;    
    
    /**
     * Création du constructeur
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRepository
     */
    function __construct(PlaylistRepository $playlistRepository,CategorieRepository $categorieRepository, FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }
    
    /**
     * Création de la route qui mène vers les playlists
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    public function index(): Response{
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::RETOURNEPLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories            
        ]);
    }

    /**
     * Tri des enregistrements selon le champ et l'ordre
     * @Route("/playlists/tri/{champ}/{ordre}", name="playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response{
        switch ($champ) {
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "nbformations":
                $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
                break;
            default:
                error();
                break;     
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::RETOURNEPLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories   
                
        ]);
    }         
    
    /**
     * Tri des enregistrements en fonction du champ et de l'ordre et de la table si la table n'est pas vide
     * Tri des enregistrements en fonction du champ et de l'ordre si la table est vide
     * Récupère les enregistrements selon le champ, la valeur et la table
     * @Route("/playlists/recherche/{champ}/{table}", name="playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
    
        $valeur = $request->get("recherche");
        if ($table != "")
        {
            $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        }else{
            $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        }
        $categories = $this->categorieRepository->findAll();
            return $this->render(self::RETOURNEPLAYLISTS, [
                
                'playlists' => $playlists,
                'categories' => $categories,     
                'valeur' => $valeur,
                'table' => $table
            ]);
    }  
    
    /**
     * Récupère les informations d'une formation 
     * @Route("/playlists/playlist/{id}", name="playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
     
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);        
    }       
    
}
