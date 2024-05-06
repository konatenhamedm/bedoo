<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Contrat;
use App\Repository\AppartementRepository;
use App\Repository\ContratRepository;
use App\Repository\LocataireRepository;
use App\Repository\NatureRepository;
use App\Repository\ProprietaireRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/contrat')]
class ApiContratController extends ApiInterface
{


    #[Route('/', methods: ['GET'])]
    /**
     * Retourne la liste des contrats.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Contrat::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'contrats')]
    // #[Security(name: 'Bearer')]
    public function index(ContratRepository $contratRepository): Response
    {
        try {

            $contrats = $contratRepository->findAll();
            $response = $this->response($contrats);
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
            items: new OA\Items(ref: new Model(type: Contrat::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'contrats')]
    //#[Security(name: 'Bearer')]
    public function getOne(?Contrat $contrat)
    {

        try {
            if ($contrat) {
                $response = $this->response($contrat);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($contrat);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }



    #[Route('/create',  methods: ['POST'])]
    /**
     * Permet de créer un(e) contrats.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Contrat::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                //new OA\Property(property: 'locataire', type: 'integer'),
                new OA\Property(property: 'proprietaire', type: 'integer'),
                new OA\Property(property: 'loyer', type: 'string'),
                new OA\Property(property: 'nature', type: 'integer'),
                new OA\Property(property: 'montantAvance', type: 'string'),
                new OA\Property(property: 'montantCaution', type: 'string'),
                new OA\Property(property: 'dateProchainVersement', type: 'string'),
                new OA\Property(property: 'jourMoisPaiement', type: 'string'),
                new OA\Property(property: 'nombreMoisAvance', type: 'integer'),
                new OA\Property(property: 'paiementPartiel', type: 'boolean'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'contrats')]
    //#[Security(name: 'Bearer')]
    public function create(
        Request $request,
        ContratRepository $contratRepository,
        LocataireRepository $locataireRepository,
        ProprietaireRepository $proprietaireRepository,
        NatureRepository $natureRepository,
        AppartementRepository $appartementRepository
    ): Response {
        try {
            $data = json_decode($request->getContent());


            $contrat = new Contrat();
            $contrat->setAppartement($appartementRepository->find($data->appartement));
            // $contrat->setLocataire($locataireRepository->find($data->locataire));
            $contrat->setLoyer($data->loyer);
            $contrat->setNature($natureRepository->find($data->nature));
            $contrat->setMontantAvance($data->montantAvance);
            $contrat->setMontantCaution($data->montantCaution);
            $contrat->setDateProchainVersement($data->DateProchainVersement);
            $contrat->setJourMoisPaiement($data->jourMoisPaiement);
            $contrat->setNombreMoisAvance($data->nombreMoisAvance);
            $contrat->setPaiementPartiel($data->paiementPartiel);
            //$contrat->setScanContrat($data->paiementPartiel);

            // On sauvegarde en base
            $contratRepository->add($contrat, true);

            // On retourne la confirmation
            $response = $this->response($contrat);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }
    #[Route('/add/locataire/{id}',  methods: ['POST'])]
    /**
     * Permet de mettre à jour un(e) contrats en ajoutant le locataire sil valide l'invitation.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Contrat::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'locataire', type: 'integer'),
                //new OA\Property(property: 'datevalidation', type: 'string')
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'contrats')]
    //#[Security(name: 'Bearer')]
    public function addLocataire(
        Request $request,
        Contrat $contrat,
        ContratRepository $contratRepository,
        LocataireRepository $locataireRepository,
        AppartementRepository $appartementRepository
    ): Response {
        try {
            $data = json_decode($request->getContent());


            if ($contrat) {
                $contrat->setLocataire($locataireRepository->find($data->locataire));
                $contrat->setDateValidation(new DateTime());
                $contratRepository->add($contrat, true);

                $appartement = $contrat->getAppartement();
                $appartement->setOccupe(true);

                $appartementRepository->add($appartement, true);
            } else {
                $this->setMessage("Cette ressource est inexsitante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }


            // On sauvegarde en base


            // On retourne la confirmation
            $response = $this->response($contrat);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', methods: ['PUT', 'POST'])]
    /**
     * Permet de mettre à jour un(e) contrats.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Contrat::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                //new OA\Property(property: 'locataire', type: 'integer'),
                new OA\Property(property: 'proprietaire', type: 'integer'),
                new OA\Property(property: 'loyer', type: 'string'),
                new OA\Property(property: 'nature', type: 'integer'),
                new OA\Property(property: 'montantAvance', type: 'string'),
                new OA\Property(property: 'montantCaution', type: 'string'),
                new OA\Property(property: 'dateProchainVersement', type: 'string'),
                new OA\Property(property: 'jourMoisPaiement', type: 'string'),
                new OA\Property(property: 'nombreMoisAvance', type: 'integer'),
                new OA\Property(property: 'paiementPartiel', type: 'boolean'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'contrats')]
    //#[Security(name: 'Bearer')]
    public function update(
        Request $request,
        ?Contrat $contrat,
        ContratRepository $contratRepository,
        NatureRepository $natureRepository,
        AppartementRepository $appartementRepository
    ): Response {
        try {
            $data = json_decode($request->getContent());


            if ($contrat != null) {

                $contrat->setAppartement($appartementRepository->find($data->appartement));
                // $contrat->setLocataire($locataireRepository->find($data->locataire));
                $contrat->setLoyer($data->loyer);
                $contrat->setNature($natureRepository->find($data->nature));
                $contrat->setMontantAvance($data->montantAvance);
                $contrat->setMontantCaution($data->montantCaution);
                $contrat->setDateProchainVersement($data->DateProchainVersement);
                $contrat->setJourMoisPaiement($data->jourMoisPaiement);
                $contrat->setNombreMoisAvance($data->nombreMoisAvance);
                $contrat->setPaiementPartiel($data->paiementPartiel);
                // On sauvegarde en base
                $contratRepository->add($contrat, true);

                // On retourne la confirmation
                $response = $this->response($contrat);
            } else {
                $this->setMessage("Cette ressource est inexsitante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }

    //const TAB_ID = 'parametre-tabs';

    #[Route('/delete/{id}',  methods: ['DELETE'])]
    /**
     * permet de supprimer un(e) contrat.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Contrat::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'contrats')]
    //#[Security(name: 'Bearer')]
    public function delete(Request $request, Contrat $contrat, ContratRepository $contratRepository): Response
    {
        try {

            if ($contrat != null) {

                $contratRepository->remove($contrat, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($contrat);
            } else {
                $this->setMessage("Cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
    #[Route('/resilier/{id}',  methods: ['POST', 'PUT'])]
    /**
     * permet de supprimer un(e) contrat.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Contrat::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                //new OA\Property(property: 'locataire', type: 'integer'),
                new OA\Property(property: 'motif', type: 'string')
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'contrats')]
    //#[Security(name: 'Bearer')]
    public function resiliation(Request $request, Contrat $contrat, AppartementRepository $appartementRepository, ContratRepository $contratRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            if ($contrat != null) {


                $contrat->setMotif($data->motif);
                $contrat->setDateResiliation(new DateTime());
                $contratRepository->add($contrat, true);

                $appartement = $contrat->getAppartement();
                $appartement->setOccupe(false);

                $appartementRepository->add($appartement, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($contrat);
            } else {
                $this->setMessage("Cette ressource est inexistante");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }

    #[Route('/delete/all',  methods: ['DELETE'])]
    /**
     * Permet de supprimer plusieurs contrats.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Contrat::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'contrats')]
    //#[Security(name: 'Bearer')]
    public function deleteAll(Request $request, ContratRepository $contratRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            foreach ($data->ids as $key => $value) {
                $contrat = $contratRepository->find($value['id']);

                if ($contrat != null) {
                    $contratRepository->remove($contrat);
                }
            }
            $this->setMessage("Operation effectuées avec success");
            $response = $this->response(null);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }
        return $response;
    }
}
