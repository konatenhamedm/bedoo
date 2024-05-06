<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Civilite;
use App\Repository\CiviliteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

#[Route('/api/civilite')]
class ApiCiviliteController extends ApiInterface
{
    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Civilite::class, 'a');

        $nb = $query->getQuery()->getSingleScalarResult();
        if ($nb == 0) {
            $nb = 1;
        } else {
            $nb = $nb + 1;
        }
        return ('DEP' . date("m", strtotime("now")) . str_pad($nb, 3, '0', STR_PAD_LEFT));
    }

    #[Route('/', methods: ['GET'])]
    /**
     * Retourne la liste des civilites.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Civilite::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'civilites')]
    //#[Security(name: 'Bearer')]
    public function index(CiviliteRepository $civiliteRepository): Response
    {
        try {

            $civilites = $civiliteRepository->findAll();
            $response = $this->response($civilites);
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
            items: new OA\Items(ref: new Model(type: Civilite::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'code',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'civilites')]
    //#[Security(name: 'Bearer')]
    public function getOne(?Civilite $civilite)
    {

        try {
            if ($civilite) {
                $response = $this->response($civilite);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($civilite);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create',  methods: ['POST'])]
    /**
     * Permet de créer un(e) civilites.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Civilite::class, groups: ['full']))
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
    #[OA\Tag(name: 'civilites')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, CiviliteRepository $civiliteRepository): Response
    {
        try {
            $data = json_decode($request->getContent());


            $civilite = new Civilite();
            $civilite->setCode($this->numero());
            $civilite->setLibelle($data->libelle);

            // On sauvegarde en base
            $civiliteRepository->add($civilite, true);

            // On retourne la confirmation
            $response = $this->response($civilite);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', methods: ['PUT', 'POST'])]
    /**
     * Permet de mettre à jour un(e) civilites.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Civilite::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'code', type: 'string'),
                new OA\Property(property: 'libelle', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'civilites')]
    // #[Security(name: 'Bearer')]
    public function update(Request $request, ?Civilite $civilite, CiviliteRepository $civiliteRepository): Response
    {
        try {
            $data = json_decode($request->getContent());


            if ($civilite != null) {


                $civilite->setLibelle($data->libelle);

                // On sauvegarde en base
                $civiliteRepository->add($civilite, true);

                // On retourne la confirmation
                $response = $this->response($civilite);
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
     * permet de supprimer un(e) civilite.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Civilite::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'civilites')]
    //#[Security(name: 'Bearer')]
    public function delete(Request $request, Civilite $civilite, CiviliteRepository $civiliteRepository): Response
    {
        try {

            if ($civilite != null) {

                $civiliteRepository->remove($civilite, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($civilite);
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
     * Permet de supprimer plusieurs civilites.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Civilite::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'civilites')]
    //#[Security(name: 'Bearer')]
    public function deleteAll(Request $request, CiviliteRepository $civiliteRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            foreach ($data->ids as $key => $value) {
                $civilite = $civiliteRepository->find($value['id']);

                if ($civilite != null) {
                    $civiliteRepository->remove($civilite);
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
