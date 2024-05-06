<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use App\Entity\Appartement;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Batis;
use App\Entity\Quartier;
use App\Repository\AppartementRepository;
use App\Repository\BatisRepository;
use App\Repository\ContratRepository;
use App\Repository\LocataireRepository;
use App\Repository\ProprietaireRepository;
use App\Repository\QuartierRepository;
use App\Repository\TypeMaisonRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

#[Route('/api/batis')]
class ApiMaisonController extends ApiInterface
{


    #[Route('/liste/batis/proprietaire/{idProprietaire}', methods: ['GET'])]
    /**
     * Retourne la liste des maison du proprietaire.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Batis::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'batis')]
    //#[Security(name: 'Bearer')]
    public function index(BatisRepository $batisRepository, $idProprietaire, ProprietaireRepository $proprietaireRepository): Response
    {
        try {

            $dataBatis = [];
            $dataAppartements = [];
            $i = 0;
            $j = 0;

            $batis = $batisRepository->findBy(['proprietaire' => $proprietaireRepository->findOneBy(['code' => $idProprietaire])]);

            foreach ($batis as $key => $batis) {
                $dataBatis[$i]['id'] = $batis->getId();
                $dataBatis[$i]['libelle'] = $batis->getLibelle();
                $dataBatis[$i]['quartier'] = $batis->getQuartier()->getLibelle();
                $dataBatis[$i]['ville'] = $batis->getQuartier()->getVille()->getLibelle();

                /* foreach ($batis->getAppartements() as $key => $appart) {
                    $dataBatis[$i]['id'] = $batis->getId();
                } */
            }


            $response = $this->response($dataBatis);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }
    #[Route('/liste/batis/proprietaire/{idLocataire}', methods: ['GET'])]
    /**
     * Retourne la liste des maison du locataire.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Batis::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'batis')]
    //#[Security(name: 'Bearer')]
    public function indexLocataire(ContratRepository $contratRepository, $idLocataire, LocataireRepository $locataireRepository): Response
    {
        try {

            $dataBatis = [];
            $i = 0;
            $batis = $contratRepository->findBy(['locataire' => $locataireRepository->findOneBy(['code' => $idLocataire])]);

            foreach ($batis as $key => $batis) {
                $dataBatis[$i]['idContrat'] = $batis->getId();
                $dataBatis[$i]['idBatis'] = $batis->getAppartement()->getBatis()->getId();
                $dataBatis[$i]['libelle'] = $batis->getAppartement()->getBatis()->getLibelle();
                $dataBatis[$i]['quartier'] = $batis->getAppartement()->getBatis()->getQuartier()->getLibelle();
                $dataBatis[$i]['ville'] = $batis->getAppartement()->getBatis()->getQuartier()->getVille()->getLibelle();

                /* foreach ($batis->getAppartements() as $key => $appart) {
                    $dataBatis[$i]['id'] = $batis->getId();
                } */
            }


            $response = $this->response($dataBatis);
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
            items: new OA\Items(ref: new Model(type: Batis::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'batis')]
    #[Security(name: 'Bearer')]
    public function getOne(?Batis $batis)
    {

        try {
            if ($batis) {
                $response = $this->response($batis);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($batis);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create',  methods: ['POST'])]
    /**
     * Permet de créer un(e) batis.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Batis::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'libelle', type: 'string'),
                new OA\Property(property: 'proprietaire', type: 'integer'),
                new OA\Property(property: 'titreFoncier', type: 'string'),
                new OA\Property(property: 'ilot', type: 'string'),
                new OA\Property(property: 'lot', type: 'string'),
                new OA\Property(property: 'quartier', type: 'integer'),
                new OA\Property(property: 'typeMaison', type: 'integer'),
                new OA\Property(property: 'adresseMaison', type: 'string'),
            ],

            //ref: new Model(type: Batis::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'batis')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, BatisRepository $batisRepository, ProprietaireRepository $proprietaireRepository, QuartierRepository $quartierRepository, TypeMaisonRepository $typeMaisonRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            $batis = $batisRepository->findOneBy(array('code' => $data->code));
            if ($batis == null) {
                $batis = new Batis();
                $batis->setLibelle($data->libelle);
                $batis->setProprietaire($proprietaireRepository->find($data->proprietaire));
                $batis->setTitreFoncier($data->titreFoncier);
                $batis->setIlot($data->ilot);
                $batis->setLot($data->lot);
                $batis->setQuartier($quartierRepository->find($data->quartier));
                $batis->setTypeMaison($typeMaisonRepository->find($data->typeMaison));
                $batis->setAdresseMaison($data->adresseMaison);

                // On sauvegarde en base
                $batisRepository->add($batis, true);

                // On retourne la confirmation
                $response = $this->response($batis);
            } else {
                $this->setMessage("Cette ressource existe deja en base");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }
    #[Route('/create/with/appartement/together',  methods: ['POST'])]
    /**
     * Permet de créer un(e) batis d'une autre maniere avec les appartements.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Batis::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'libelle', type: 'string'),
                new OA\Property(property: 'proprietaire', type: 'integer'),
                new OA\Property(property: 'titreFoncier', type: 'string'),
                new OA\Property(property: 'ilot', type: 'string'),
                new OA\Property(property: 'lot', type: 'string'),
                new OA\Property(property: 'quartier', type: 'integer'),
                new OA\Property(property: 'typeMaison', type: 'integer'),
                new OA\Property(property: 'adresseMaison', type: 'string'),
                //new OA\Property(property: 'appartements', type: 'array'),


            ],

            //ref: new Model(type: Batis::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'batis')]
    //#[Security(name: 'Bearer')]
    public function createSeconde(Request $request, BatisRepository $batisRepository, AppartementRepository $appartementRepository, ProprietaireRepository $proprietaireRepository, QuartierRepository $quartierRepository, TypeMaisonRepository $typeMaisonRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            $batis = new Batis();
            $batis->setLibelle($data->libelle);
            $batis->setProprietaire($proprietaireRepository->find($data->proprietaire));
            $batis->setTitreFoncier($data->titreFoncier);
            $batis->setIlot($data->ilot);
            $batis->setLot($data->lot);
            $batis->setQuartier($quartierRepository->find($data->quartier));
            $batis->setTypeMaison($typeMaisonRepository->find($data->typeMaison));
            $batis->setAdresseMaison($data->adresseMaison);

            foreach ($data->appartements as $key => $appart) {
                $appartement = new Appartement();

                $appartement->setNumeroEtage($appart['numeroEtage']);
                $appartement->setBatis($batis);
                $appartement->setLoyer($appart['loyer']);
                $appartement->setNombrePiece($appart['nombrePiece']);
                $appartement->setDetails($appart['detatils']);
                $appartement->setOccupe(false);
                $appartement->setNumeroAppartement($appart['numeroAppartement']);
                $appartementRepository->add($appartement, true);
            }

            // On sauvegarde en base
            $batisRepository->add($batis, true);

            // On retourne la confirmation
            $response = $this->response($batis);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', methods: ['PUT', 'POST'])]
    /**
     * Permet de mettre à jour un(e) batis.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Batis::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'libelle', type: 'string'),
                new OA\Property(property: 'proprietaire', type: 'integer'),
                new OA\Property(property: 'titreFoncier', type: 'string'),
                new OA\Property(property: 'ilot', type: 'string'),
                new OA\Property(property: 'lot', type: 'string'),
                new OA\Property(property: 'quartier', type: 'integer'),
                new OA\Property(property: 'typeMaison', type: 'integer'),
                new OA\Property(property: 'adresseMaison', type: 'string'),
            ],

            //ref: new Model(type: Batis::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'batis')]
    // #[Security(name: 'Bearer')]
    public function update(Request $request, Batis $batis, BatisRepository $batisRepository, ProprietaireRepository $proprietaireRepository, QuartierRepository $quartierRepository, TypeMaisonRepository $typeMaisonRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            //$batis = $batisRepository->find($id);
            if ($batis == null) {
                $batis = new Batis();
                $batis->setLibelle($data->libelle);
                $batis->setProprietaire($proprietaireRepository->find($data->proprietaire));
                $batis->setTitreFoncier($data->titreFoncier);
                $batis->setIlot($data->ilot);
                $batis->setLot($data->lot);
                $batis->setQuartier($quartierRepository->find($data->quartier));
                $batis->setTypeMaison($typeMaisonRepository->find($data->typeMaison));
                $batis->setAdresseMaison($data->adresseMaison);

                // On sauvegarde en base
                $batisRepository->add($batis, true);

                // On retourne la confirmation
                $response = $this->response($batis);
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
     * permet de supprimer un(e) batis.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Batis::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'batis')]
    //#[Security(name: 'Bearer')]
    public function delete(Request $request, Batis $batis, BatisRepository $batisRepository): Response
    {
        try {

            if ($batis != null) {

                $batisRepository->remove($batis, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($batis);
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
     * Permet de supprimer plusieurs batis.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Batis::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'batis')]
    //#[Security(name: 'Bearer')]
    public function deleteAll(Request $request, BatisRepository $batisRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            foreach ($data->ids as $key => $value) {
                $batis = $batisRepository->find($value['id']);

                if ($batis != null) {
                    $batisRepository->remove($batis);
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
