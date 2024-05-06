<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Appartement;
use App\Repository\AppartementRepository;
use App\Repository\BatisRepository;
use App\Repository\ProprietaireRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

#[Route('/api/appartement')]
class ApiAppartementController extends ApiInterface
{


    #[Route('/', methods: ['GET'])]
    /**
     * Retourne la liste des appartements.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Appartement::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'appartements')]
    //#[Security(name: 'Bearer')]
    public function index(AppartementRepository $appartementRepository): Response
    {
        try {
            $i = 0;
            $dataAppartements = [];
            $appartements = $appartementRepository->findAll();

            foreach ($appartements as $key => $appart) {

                $dataAppartements[$i]['id'] = $appart->getId();
                $dataAppartements[$i]['numeroEtatge'] = $appart->getNumeroEtage();
                $dataAppartements[$i]['numeroAppartement'] = $appart->getNumeroAppartement();
                $dataAppartements[$i]['nombrePiece'] = $appart->getNombrePiece();
                $dataAppartements[$i]['occupe'] = $appart->isOccupe();
                $dataAppartements[$i]['loyer'] = $appart->getLoyer();
            }
            $response = $this->response($dataAppartements);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }

    #[Route('/appartement/libre/{idProprietaire}', methods: ['GET'])]
    /**
     * Retourne la liste des appartements libre d'une proprio.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Appartement::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'appartements')]
    //#[Security(name: 'Bearer')]
    public function indexAppartementLibre(AppartementRepository $appartementRepository, $idProprietaire, ProprietaireRepository $proprietaireRepository): Response
    {
        try {
            $i = 0;
            $dataAppartements = [];
            $appartements = $appartementRepository->getAllAppartSpare($proprietaireRepository->findOneBy(['code' => $idProprietaire]));

            //dd($appartements);
            foreach ($appartements as $key => $appart) {

                $dataAppartements[$i]['id'] = $appart->getId();
                $dataAppartements[$i]['numeroEtatge'] = $appart->getNumeroEtage();
                $dataAppartements[$i]['numeroAppartement'] = $appart->getNumeroAppartement();
                $dataAppartements[$i]['nombrePiece'] = $appart->getNombrePiece();
                $dataAppartements[$i]['occupe'] = $appart->isOccupe();
                $dataAppartements[$i]['loyer'] = $appart->getLoyer();
            }

            if ($appartements) {
                $response = $this->response($dataAppartements);
            } else {
                $this->setMessage("Il n'y aucune données disponibles");
                $response = $this->response('');
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
            items: new OA\Items(ref: new Model(type: Appartement::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'code',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'appartements')]
    #[Security(name: 'Bearer')]
    public function getOne(?Appartement $appartement)
    {

        try {
            if ($appartement) {
                $response = $this->response($appartement);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($appartement);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create',  methods: ['POST'])]
    /**
     * Permet de créer un(e) appartements.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Appartement::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'numeroEtage', type: 'string'),
                new OA\Property(property: 'batis', type: 'integer'),
                new OA\Property(property: 'loyer', type: 'string'),
                new OA\Property(property: 'nombrePiece', type: 'string'),
                new OA\Property(property: 'detatils', type: 'string'),
                new OA\Property(property: 'numeroAppartement', type: 'string'),
            ],

            //ref: new Model(type: Appartement::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'appartements')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, AppartementRepository $appartementRepository, BatisRepository $batisRepository): Response
    {
        try {
            $data = json_decode($request->getContent());


            $appartement = new Appartement();

            $appartement->setNumeroEtage($data->numeroEtage);
            $appartement->setBatis($batisRepository->find($data->batis));
            $appartement->setLoyer($data->loyer);
            $appartement->setNombrePiece($data->nombrePiece);
            $appartement->setDetails($data->detatils);
            $appartement->setOccupe(false);
            $appartement->setNumeroAppartement($data->numeroAppartement);
            $appartementRepository->add($appartement, true);

            // On sauvegarde en base
            $appartementRepository->add($appartement, true);

            // On retourne la confirmation
            $response = $this->response($appartement);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', methods: ['PUT', 'POST'])]
    /**
     * Permet de mettre à jour un(e) appartements.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Appartement::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'numeroEtage', type: 'string'),
                new OA\Property(property: 'batis', type: 'integer'),
                new OA\Property(property: 'loyer', type: 'string'),
                new OA\Property(property: 'nombrePiece', type: 'string'),
                new OA\Property(property: 'detatils', type: 'string'),
                new OA\Property(property: 'numeroAppartement', type: 'string'),
            ],

            //ref: new Model(type: Appartement::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'appartements')]
    // #[Security(name: 'Bearer')]
    public function update(Request $request, Appartement $appartement, BatisRepository $batisRepository, AppartementRepository $appartementRepository): Response
    {
        try {
            $data = json_decode($request->getContent());


            if ($appartement != null) {

                $appartement->setNumeroEtage($data->numeroEtage);
                $appartement->setBatis($batisRepository->find($data->batis));
                $appartement->setLoyer($data->loyer);
                $appartement->setNombrePiece($data->nombrePiece);
                $appartement->setDetails($data->detatils);
                // $appartement->setOccupe(false);
                $appartement->setNumeroAppartement($data->numeroAppartement);

                // On sauvegarde en base
                $appartementRepository->add($appartement, true);

                // On retourne la confirmation
                $response = $this->response($appartement);
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
     * permet de supprimer un(e) appartement.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Appartement::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'appartements')]
    //#[Security(name: 'Bearer')]
    public function delete(Request $request, Appartement $appartement, AppartementRepository $appartementRepository): Response
    {
        try {

            if ($appartement != null) {

                $appartementRepository->remove($appartement, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($appartement);
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
     * Permet de supprimer plusieurs appartements.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Appartement::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'appartements')]
    //#[Security(name: 'Bearer')]
    public function deleteAll(Request $request, AppartementRepository $appartementRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            foreach ($data->ids as $key => $value) {
                $appartement = $appartementRepository->find($value['id']);

                if ($appartement != null) {
                    $appartementRepository->remove($appartement);
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
