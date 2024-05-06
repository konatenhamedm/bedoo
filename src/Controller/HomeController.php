<?php

namespace App\Controller;

use App\Repository\VersementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    protected $em;
    protected $versementRepository;

    public function __construct(EntityManagerInterface $em, VersementRepository $versementRepository)
    {


        //$this->utils = $utils;
        $this->em = $em;
        $this->versementRepository = $versementRepository;
    }


    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/HomeController.php',
        ]);
    }

    #[Route('/first', name: 'app_home_first')]
    public function first(): Response
    {
        return $this->json("cool");
    }

    #[Route('/seconde', name: 'app_home_seconde')]
    public function updateData(): Response
    {
        $versement = $this->versementRepository->find(1);
        $versement->setMontant('75');
        $this->versementRepository->add($versement, true);


        return $this->json("cool uuuu");
    }
}
