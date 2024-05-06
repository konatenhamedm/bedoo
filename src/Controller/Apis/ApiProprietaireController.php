<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Entity\Proprietaire;
use App\Repository\ProprietaireRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/proprietaire')]
class ApiProprietaireController extends ApiInterface
{


    #[Route('/', methods: ['GET'])]
    /**
     * Retourne la liste des proprietaires.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Proprietaire::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'proprietaires')]
    // #[Security(name: 'Bearer')]
    public function index(ProprietaireRepository $proprietaireRepository): Response
    {
        try {

            $proprietaires = $proprietaireRepository->findAll();
            $response = $this->response($proprietaires);
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
            items: new OA\Items(ref: new Model(type: Proprietaire::class, groups: ['full']))
        )
    )]
    #[OA\Parameter(
        name: 'code',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'proprietaires')]
    //#[Security(name: 'Bearer')]
    public function getOne(?Proprietaire $proprietaire)
    {

        try {
            if ($proprietaire) {
                $response = $this->response($proprietaire);
            } else {
                $this->setMessage('Cette ressource est inexistante');
                $this->setStatusCode(300);
                $response = $this->response($proprietaire);
            }
        } catch (\Exception $exception) {
            $this->setMessage($exception->getMessage());
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/create/{idProprietaire}',  methods: ['POST'])]
    /**
     * Permet de créer un(e) proprietaires.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Proprietaire::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'nom', type: 'string'),
                new OA\Property(property: 'prenoms', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'proprietaires')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, ProprietaireRepository $proprietaireRepository, $idProprietaire): Response
    {
        try {
            $data = json_decode($request->getContent());
            $proprietaire = $proprietaireRepository->findOneBy(['code' => $idProprietaire]);
            if ($proprietaire == null) {
                $proprietaire = new Proprietaire();
                $proprietaire->setNom($data->nom);
                $proprietaire->setPrenoms($data->prenoms);
                $proprietaire->setCode($idProprietaire);


                // On sauvegarde en base
                $proprietaireRepository->add($proprietaire, true);

                // On retourne la confirmation
                $response = $this->response($proprietaire);
            } else {
                $this->setMessage("Cette ressource existe deja en base comme proprietaire");
                $this->setStatusCode(300);
                $response = $this->response(null);
            }
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }


        return $response;
    }


    #[Route('/update/{idProprietaire}', methods: ['PUT', 'POST'])]
    /**
     * Permet de mettre à jour un(e) proprietaires.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Proprietaire::class, groups: ['full']))
        )
    )]
    #[OA\RequestBody(
        description: 'The object',
        required: false,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'nom', type: 'string'),
                new OA\Property(property: 'prenoms', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'proprietaires')]
    //#[Security(name: 'Bearer')]
    public function update(Request $request, ProprietaireRepository $proprietaireRepository, $idProprietaire): Response
    {
        try {
            $data = json_decode($request->getContent());
            $proprietaire = $proprietaireRepository->findOneBy(['code' => $idProprietaire]);

            if ($proprietaire != null) {

                $proprietaire->setNom($data->nom);
                $proprietaire->setPrenoms($data->prenoms);

                // On sauvegarde en base
                $proprietaireRepository->add($proprietaire, true);

                // On retourne la confirmation
                $response = $this->response($proprietaire);
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
     * permet de supprimer un(e) proprietaire.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Proprietaire::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'proprietaires')]
    //#[Security(name: 'Bearer')]
    public function delete(Request $request, Proprietaire $proprietaire, ProprietaireRepository $proprietaireRepository): Response
    {
        try {

            if ($proprietaire != null) {

                $proprietaireRepository->remove($proprietaire, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuées avec success");
                $response = $this->response($proprietaire);
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
     * Permet de supprimer plusieurs proprietaires.
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the rewards of an user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Proprietaire::class, groups: ['full']))
        )
    )]
    #[OA\Tag(name: 'proprietaires')]
    //#[Security(name: 'Bearer')]
    public function deleteAll(Request $request, ProprietaireRepository $proprietaireRepository): Response
    {
        try {
            $data = json_decode($request->getContent());

            foreach ($data->ids as $key => $value) {
                $proprietaire = $proprietaireRepository->find($value['id']);

                if ($proprietaire != null) {
                    $proprietaireRepository->remove($proprietaire);
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
