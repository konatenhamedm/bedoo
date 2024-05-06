<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\MotifResiliation;
use App\Repository\MotifResiliationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/motifResiliation')]
class ApiMotifResiliationController extends ApiInterface
{


    #[Route('/', methods: ['GET'])]
    /**
     * Retourne la liste des motifResiliations.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: MotifResiliation::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'motifResiliations')]
    ///#[Security(name: 'Bearer')]
    public function index(MotifResiliationRepository $motifResiliationRepository): Response
    {
        try {

            $motifResiliations = $motifResiliationRepository->findAll();
            $response = $this->response($motifResiliations);
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
            items: new OA\Items(ref: new Model(type: MotifResiliation::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'code',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'motifResiliations')]
    //#[Security(name: 'Bearer')]
    public function getOne(?MotifResiliation $motifResiliation)
    {

        try {
            if ($motifResiliation) {
                $response = $this->response($motifResiliation);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($motifResiliation);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create',  methods: ['POST'])]
    /**
     * Permet de créer un(e) motifResiliations.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: MotifResiliation::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                //new OA\Property(property: 'code', type: 'string'),
                new OA\Property(property: 'libelle', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )
    )]
    #[OA\Tag(name: 'motifResiliations')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, MotifResiliationRepository $motifResiliationRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            $motifResiliation = $motifResiliationRepository->findOneBy(array('libelle' => $data->libelle));
            if ($motifResiliation == null) {
                $motifResiliation = new MotifResiliation();
                //$motifResiliation->setCode($data->code);
                $motifResiliation->setLibelle($data->libelle);

                // On sauvegarde en base
                $motifResiliationRepository->add($motifResiliation, true);

                // On retourne la confirmation
                $response = $this->response($motifResiliation);
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


    #[Route('/update/{id}', methods: ['PUT', 'POST'])]
    /**
     * Permet de mettre à jour un(e) motifResiliations.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: MotifResiliation::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                // new OA\Property(property: 'code', type: 'string'),
                new OA\Property(property: 'libelle', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'motifResiliations')]
    //#[Security(name: 'Bearer')]
    public function update(Request $request, ?MotifResiliation $motifResiliation, MotifResiliationRepository $motifResiliationRepository): Response
    {
        try {
            $data = json_decode($request->getContent());


            if ($motifResiliation != null) {

                // $motifResiliation->setCode($data->code);
                $motifResiliation->setLibelle($data->libelle);

                // On sauvegarde en base
                $motifResiliationRepository->add($motifResiliation, true);

                // On retourne la confirmation
                $response = $this->response($motifResiliation);
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
     * permet de supprimer un(e) motifResiliation.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: MotifResiliation::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'motifResiliations')]
    //#[Security(name: 'Bearer')]
    public function delete(Request $request, MotifResiliation $motifResiliation, MotifResiliationRepository $motifResiliationRepository): Response
    {
        try {

            if ($motifResiliation != null) {

                $motifResiliationRepository->remove($motifResiliation, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($motifResiliation);
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
     * Permet de supprimer plusieurs motifResiliations.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: MotifResiliation::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'motifResiliations')]
    //#[Security(name: 'Bearer')]
    public function deleteAll(Request $request, MotifResiliationRepository $motifResiliationRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            foreach ($data->ids as $key => $value) {
                $motifResiliation = $motifResiliationRepository->find($value['id']);

                if ($motifResiliation != null) {
                    $motifResiliationRepository->remove($motifResiliation);
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
