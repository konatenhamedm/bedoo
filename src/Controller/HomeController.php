<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Test;
use App\Entity\Versement;
use App\Repository\ContratRepository;
use App\Repository\FactureRepository;
use App\Repository\MoisRepository;
use App\Repository\TestRepository;
use App\Repository\VersementRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    protected $em;
    protected $versementRepository;
    protected $factureRepository;
    protected $contratRepository;
    protected $moisRepository;

    public function __construct(EntityManagerInterface $em, VersementRepository $versementRepository, FactureRepository $factureRepository, ContratRepository $contratRepository, MoisRepository $moisRepository)
    {


        //$this->utils = $utils;
        $this->em = $em;
        $this->versementRepository = $versementRepository;
        $this->factureRepository = $factureRepository;
        $this->contratRepository = $contratRepository;
        $this->moisRepository = $moisRepository;
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

    #[Route('/addData', name: 'app_home_add')]
    public function add(): Response
    {
        // dd($this->contratRepository->find(1));
        $date = new DateTime();

        // 👇 modify the date
        $date->modify("last day of this month");

        dd($date->format("d"));
        /* $test = new Test();
        $test->setName('test');
        $this->em->persist($test);
        $this->em->flush(); */
        return $this->json("cool");
    }

    private function numeroFacture()

    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Facture::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ('FACT' . '-' . date("y") . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }

    private function numeroVersements()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Versement::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ('VERS' . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
        // return (date("y") . '-' . 'ESP' . '-' . date("m", strtotime("now")) . '-' . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }

    public function creationFacture($contrat, bool $jourReception): Facture
    {
        $lastDayOfMonth = (new DateTime())->modify("last day of this month")->format("d");
        $dateActuelle = new \DateTime();
        $dateMoisSuivant = $dateActuelle->add(new \DateInterval('P1M'));
        $dateMoisSuivant->setDate($dateMoisSuivant->format('Y'), $dateMoisSuivant->format('m'), $contrat->getJourMoisPaiement() ? intval($contrat->getJourMoisPaiement()) : 5);


        $facture = new Facture();
        $facture->setDateCreation(new DateTime());
        $facture->setContrat($contrat);
        $facture->setMontant($contrat->getLoyer());
        $facture->setNumero($this->numeroFacture());


        if ($jourReception == false) {

            if ($contrat->isFirstPay() == false) {
                $facture->setLibelleFacture($this->moisRepository->findOneBy(['numero' => (int)$contrat->getDateProchainVersement()->format("m")])->getLibelle() . ' ' . $contrat->getDateProchainVersement()->format("Y"));
                $facture->setMois($this->moisRepository->findOneBy(['numero' => (int)$contrat->getDateProchainVersement()->format("m")]));
                $facture->setDateLimitePaiment($contrat->getDateProchainVersement());
            } else {
                //TO DO REVOIR 
                $facture->setLibelleFacture($this->moisRepository->findOneBy(['numero' => (int)date("m")])->getLibelle() . ' ' . (new DateTime())->format("Y"));
                $facture->setMois($this->moisRepository->findOneBy(['numero' => (int)(int)date("m")]));
                $facture->setDateLimitePaiment($dateMoisSuivant);
            }
        } else {

            $facture->setLibelleFacture($this->moisRepository->findOneBy(['numero' => (int)date("m")])->getLibelle() . ' ' . (new DateTime())->format("Y"));
            $facture->setMois($this->moisRepository->findOneBy(['numero' => (int)date("m")]));
            $facture->setDateLimitePaiment($dateMoisSuivant);
        }

        if ($contrat->getMontantAvance() > 0 && ((int)$contrat->getMontantAvance() >= (int)$contrat->getLoyer())) {
            $facture->setStatut('solde');
            $facture->setSoldeFacture("0");
            $versement = new Versement();
            $versement->setMontant($contrat->getLoyer());
            $versement->setDatePaiement(new Datetime());
            $versement->setFacture($facture);
            $versement->setResponsablePaye('systeme');
            $this->versementRepository->add($versement, true);
            $contrat->setMontantAvance(abs((int)$contrat->getMontantAvance() - (int)$contrat->getLoyer()));
        } else {

            $facture->setStatut('pas_solde');
            $facture->setSoldeFacture($contrat->getLoyer());
        }

        if ($contrat->isFirstPay() == false)
            $contrat->setFirstPay(true);

        $this->contratRepository->add($contrat, true);
        $this->factureRepository->add($facture, true);

        return $facture;
    }

    #[Route('/seconde', name: 'app_home_seconde')]
    public function updateData(): Response
    {

        // 👇 recuepere le dernier jour du mois
        $lastDayOfMonth = (new DateTime())->modify("last day of this month")->format("d");
        $lastDate = (new DateTime())->modify("last day of this month")->format("Y-m-d");

        $contrats = $this->contratRepository->findBy(['etat' => 'actif']);

        foreach ($contrats as $key => $contrat) {
            //$verifFactureExist = $this->factureRepository->findOneBy();
            if ($contrat->isFirstPay() == false) {
                // dd((int)$contrat->getDateProchainVersement()->format("d"));{}

                if ($contrat->getDateProchainVersement()->format("Y-m-d") == (new DateTime())->format("Y-m-d")) {

                    $this->creationFacture($contrat, false);
                    /// $this->factureRepository->add($facture, true);
                }
            } else {
                if ($contrat->getJourReceptionFacture()  == null) {
                    if ($lastDate == (new DateTime())->format("Y-m-d")) {
                        if ($this->factureRepository->findOneBy(['contrat' => $contrat, 'mois' => $this->moisRepository->findOneByNumero((int)$lastDayOfMonth)]) == null) {

                            $this->creationFacture($contrat, false);
                        }
                    }
                } else {

                    if ($lastDayOfMonth == $contrat->getJourReceptionFacture()) {
                        if ($this->factureRepository->findOneBy(['contrat' => $contrat, 'mois' => $this->moisRepository->findOneByNumero((int)$contrat->getJourReceptionFacture())]) == null) {

                            $this->creationFacture($contrat, true);
                        }
                    }
                }
            }
        }




        return $this->json("rrrr");
    }
}
