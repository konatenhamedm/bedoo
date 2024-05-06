<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Controller\Apis\Config\InterfaceMethode;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Nature;
use App\Repository\NatureRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/nature')]
class ApiNatureController extends ApiInterface
{
    private function numero()
    {

        $query = $this->em->createQueryBuilder();
        $query->select("count(a.id)")
            ->from(Nature::class, 'a');

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
     * Retourne la liste des natures.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Nature::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'natures')]
    ///#[Security(name: 'Bearer')]
    public function index(NatureRepository $natureRepository): Response
    {
        try {

            $natures = $natureRepository->findAll();
            $response = $this->response($natures);
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
            items: new OA\Items(ref: new Model(type: Nature::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'code',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'natures')]
    //#[Security(name: 'Bearer')]
    public function getOne(?Nature $nature)
    {

        try {
            if ($nature) {
                $response = $this->response($nature);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($nature);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create',  methods: ['POST'])]
    /**
     * Permet de créer un(e) natures.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Nature::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [

                new OA\Property(property: 'libelle', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'natures')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, NatureRepository $natureRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            $nature = new Nature();
            $nature->setCode($this->numero());
            $nature->setLibelle($data->libelle);

            // On sauvegarde en base
            $natureRepository->add($nature, true);

            // On retourne la confirmation
            $response = $this->response($nature);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{id}', methods: ['PUT', 'POST'])]
    /**
     * Permet de mettre à jour un(e) natures.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Nature::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [

                new OA\Property(property: 'libelle', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'natures')]
    //#[Security(name: 'Bearer')]
    public function update(Request $request, Nature $nature, NatureRepository $natureRepository): Response
    {
        try {
            $data = json_decode($request->getContent());


            if ($nature != null) {

                //$nature->setCode($data->code);
                $nature->setLibelle($data->libelle);

                // On sauvegarde en base
                $natureRepository->add($nature, true);

                // On retourne la confirmation
                $response = $this->response($nature);
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
     * permet de supprimer un(e) nature.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Nature::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'natures')]
    //#[Security(name: 'Bearer')]
    public function delete(Request $request, Nature $nature, NatureRepository $natureRepository): Response
    {
        try {

            if ($nature != null) {

                $natureRepository->remove($nature, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($nature);
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
     * Permet de supprimer plusieurs natures.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Nature::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'natures')]
    //#[Security(name: 'Bearer')]
    public function deleteAll(Request $request, NatureRepository $natureRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            foreach ($data->ids as $key => $value) {
                $nature = $natureRepository->find($value['id']);

                if ($nature != null) {
                    $natureRepository->remove($nature);
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
