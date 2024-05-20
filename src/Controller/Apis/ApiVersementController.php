<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Versement;
use App\Repository\FactureRepository;
use App\Repository\VersementRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

#[Route('/api/versement')]
class ApiVersementController extends ApiInterface
{


    #[Route('/versement/facture/{idFacture}', methods: ['GET'])]
    /**
     * Retourne la liste des versements d'une facture.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Versement::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'versements')]
    //#[Security(name: 'Bearer')]
    public function index(VersementRepository $versementRepository, $idFacture, FactureRepository $factureRepository): Response
    {
        try {

            $versements = $versementRepository->findBy(['facture' => $idFacture]);

            if ($versements) {
                $response = $this->response($versements);
            } else {
                $this->setMessage(sprintf("Il n'existe pas de versement pour la facture %s", $factureRepository->find($idFacture)->getNumero()));
                $response = $this->response(null);
            }
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
            items: new OA\Items(ref: new Model(type: Versement::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'code',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'versements')]
    //#[Security(name: 'Bearer')]
    public function getOne(?Versement $versement)
    {

        try {
            if ($versement) {
                $response = $this->response($versement);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($versement);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create/facture/{idFacture}',  methods: ['POST'])]
    /**
     * Permet de créer un(e) versements.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Versement::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'montant', type: 'string'),
                new OA\Property(property: 'responsablePaye', type: 'string', description: 'la valeur peut être proprietaire ou locataire ou systeme'),
                new OA\Property(property: 'typePaiement', type: 'string', description: 'la valeur peut être partiel ou non_partiel'),
            ],

            //ref: new Model(type: Versement::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'versements')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, VersementRepository $versementRepository, FactureRepository $factureRepository, $idFacture): Response
    {
        try {
            $data = json_decode($request->getContent());

            $facture = $factureRepository->find($idFacture);

            $versement = new Versement();
            $versement->setMontant($data->montant);
            $versement->setResponsablePaye($data->responsablePaye);
            $versement->setDatePaiement(new DateTime());
            $versement->setMontant($data->montant);
            $versement->setFacture($facture);

            if ($data->typePaiement == 'partiel') {

                if ((int)$facture->getSoldeFacture() > (int)$data->montant) {
                    $facture->setSoldeFacture(abs((int)$facture->getSoldeFacture() - (int)$data->montant));
                    $versementRepository->add($versement, true);
                    $factureRepository->add($facture, true);
                } elseif ((int)$facture->getSoldeFacture() == (int)$data->montant) {
                    $facture->setSoldeFacture('0');
                    $facture->setStatut('solde');
                    $facture->setDatePaiementTotal(new DateTime());
                    $versementRepository->add($versement, true);
                    $factureRepository->add($facture, true);
                } else {

                    $this->setMessage('le montant partiel saisi est superieur au montant restant à payer');
                    $response = $this->response($facture);
                }
            } else {

                $facture->setSoldeFacture('0');
                $facture->setStatut('solde');
                $facture->setDatePaiementTotal(new DateTime());
                $versementRepository->add($versement, true);
                $factureRepository->add($facture, true);
            }



            // On sauvegarde en base


            // On retourne la confirmation
            $response = $this->response($versement);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }
}
