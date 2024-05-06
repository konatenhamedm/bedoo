<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Facture;
use App\Repository\FactureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

#[Route('/api/facture')]
class ApiFactureController extends ApiInterface
{


    #[Route('/facture/contrat/{idContrat}', methods: ['GET'])]
    /**
     * Retourne la liste des factures d'un contrat(payées et non payées).
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Facture::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'factures')]
    // #[Security(name: 'Bearer')]
    public function index(FactureRepository $factureRepository, $idContrat): Response
    {
        try {
            $dataFactures = [];
            $i = 0;


            $factures = $factureRepository->findBy(['contrat' => $idContrat]);

            foreach ($factures as $key => $facture) {
                $dataFactures[$i]['id'] = $facture->getId();
                $dataFactures[$i]['libelle'] = $facture->getLibelleFacture();
                $dataFactures[$i]['paiementPartiel'] = $facture->getContrat()->isPaiementPartiel();
                $dataFactures[$i]['montant'] = $facture->getMontant();
                $dataFactures[$i]['dateLimite'] = $facture->getDateLimitePaiment();
                $dataFactures[$i]['etat'] = $facture->getStatut();
            }
            $response = $this->response($factures);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }



    #[Route('/get/one/{id}', methods: ['GET'])]
    /**
     * Affiche une civilte en offrant un identifiant.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Facture::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'code',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'factures')]
    #[Security(name: 'Bearer')]
    public function getOne(?Facture $facture)
    {

        try {
            if ($facture) {
                $response = $this->response($facture);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($facture);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }

        return $response;
    }
}
