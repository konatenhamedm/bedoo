<?php

namespace  App\Controller\Apis;

use App\Controller\Apis\Config\ApiInterface;
use App\Entity\Proprietaire;
use App\Repository\BatisRepository;
use App\Repository\ContratRepository;
use App\Repository\FactureRepository;
use App\Repository\LocataireRepository;
use App\Repository\ProprietaireRepository;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use function Symfony\Component\String\toString;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Support\Str;

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
    #[Route('/dashboard/{userId}', methods: ['GET'])]
    /**
     * Retourne les statistiques d'un proprietaire.
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
    public function dashboard($userId, ProprietaireRepository $proprietaireRepository, LocataireRepository $locataireRepository, ContratRepository $contratRepository, BatisRepository $batiRepository, FactureRepository $factureRepository): Response
    {
        // dd($proprietaireRepository->count(['prenoms' => 'konate']));
        //dd($contratRepository->getContratAppartement($userId, 'actif'));
        //dd(count($factureRepository->getFacturePro($userId, 'pas_solde')));
        try {
            $data = [
                'nombre_locataire_total' => count($contratRepository->getContratLocataire($userId, null)),
                'nombre_locataire_contrat_actif' => count($contratRepository->getContratLocataire($userId, 'actif')),
                'nombre_locataire_contrat_resilier' => count($contratRepository->getContratLocataire($userId, 'resilier')),
                'nombre_contrat_actif' => count($contratRepository->getContratAppartement($userId, 'actif')),
                'nombre_contrat_total' => count($contratRepository->getContratAppartement($userId, null)),
                'nombre_contrat_resilier' => count($contratRepository->getContratAppartement($userId, 'resilier')),
                'nombre_batis' => $batiRepository->count(['proprietaire' => $userId]) ?? 0,
                'nombre_factures_non_paye' => count($factureRepository->getFacturePro($userId, 'pas_solde')),
                'nombre_factures_paye' => count($factureRepository->getFacturePro($userId, 'solde')),
            ];


            $response = $this->response($data);
        } catch (\Exception $exception) {
            $this->setMessage("");
            $response = $this->response(null);
        }

        // On envoie la réponse
        return $response;
    }


    #[Route('/get/one/{userId}', methods: ['GET'])]
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
    public function getOne(ProprietaireRepository $proprietaireRepository, $UserId)
    {

        try {

            $proprietaire = $proprietaireRepository->findOneBy(['code' => $UserId]);
            if ($proprietaire) {
                $response = $this->response([
                    'nom' => $proprietaire->getNom(),
                    'prenoms' => $proprietaire->getPrenoms(),
                    'code' => $UserId,
                ]);
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



    #[Route('/create',  methods: ['POST'])]
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
                new OA\Property(property: 'numero', type: 'string'),
                new OA\Property(property: 'userId', type: 'string'),
                new OA\Property(property: 'photo', type: 'string'),
                new OA\Property(property: 'verso', type: 'string'),
                new OA\Property(property: 'recto', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'proprietaires')]
    //#[Security(name: 'Bearer')]
    public function create(Request $request, ProprietaireRepository $proprietaireRepository, Utils $utils, EntityManagerInterface $em): Response
    {
        try {
            //$data = json_decode($request->getContent());
            $proprietaireVerif = $proprietaireRepository->findOneBy(['code' => $request->get('userId')]);
            if ($proprietaireVerif == null) {
                $proprietaire = new Proprietaire();
                $proprietaire->setNom($request->get('nom'));
                $proprietaire->setPrenoms($request->get('prenoms'));
                $proprietaire->setNumero($request->get('numero'));
                $proprietaire->setCode($request->get('userId'));
                $proprietaire->setEtat('En_cours_validation');

                $uploadedFile = $request->files->get('photo');
                $uploadedFileVerso = $request->files->get('verso');
                $uploadedFileRecto = $request->files->get('recto');

                $names = 'document_' . '01';
                //  $slug = 
                $filePrefix  = str_slug($names);
                //$filePrefix  = 'document_';

                $filePath = $this->getUploadDir(self::UPLOAD_PATH, true);

                if ($uploadedFile) {
                    //dd('');
                    $fichier = $utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFile, self::UPLOAD_PATH);

                    //dd($fichier);
                    if ($fichier) {
                        $proprietaire->setPhoto($fichier);
                    }
                }

                if ($uploadedFileRecto) {

                    $fichierRecto = $utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFileRecto, self::UPLOAD_PATH);

                    if ($fichierRecto) {

                        $proprietaire->setRecto($fichierRecto);
                    }
                }
                if ($uploadedFileVerso) {

                    $fichierVerso = $utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFileVerso, self::UPLOAD_PATH);

                    if ($fichierVerso) {

                        $proprietaire->setVerso($fichierVerso);
                    }
                }


                // On sauvegarde en base

                $proprietaireRepository->add($proprietaire, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuee avec succes");
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


    #[Route('/update', methods: ['POST'])]
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
                new OA\Property(property: 'numero', type: 'string'),
                new OA\Property(property: 'userId', type: 'string'),
                new OA\Property(property: 'photo', type: 'string'),
                new OA\Property(property: 'verso', type: 'string'),
                new OA\Property(property: 'recto', type: 'string'),
            ],

            //ref: new Model(type: Civilite::class, groups: ['full'])
        )

    )]
    #[OA\Tag(name: 'proprietaires')]
    //#[Security(name: 'Bearer')]
    public function update(Request $request, ProprietaireRepository $proprietaireRepository, Utils $utils): Response
    {
        try {
            $proprietaire = $proprietaireRepository->findOneBy(['code' => $request->get('userId')]);
            //dd($proprietaire);
            //$data = json_decode($request->getContent());
            if ($proprietaire != null) {

                $proprietaire->setNom($request->get('nom'));
                $proprietaire->setPrenoms($request->get('prenoms'));
                $proprietaire->setNumero($request->get('numero'));
                $proprietaire->setCode($request->get('userId'));
                //$proprietaire->setEtat('En_cours_validation');

                $uploadedFile = $request->files->get('photo');
                $uploadedFileVerso = $request->files->get('verso');
                $uploadedFileRecto = $request->files->get('recto');
                // dd('');

                $names = 'document_' . '01';
                //  $slug = 
                $filePrefix  = str_slug($names);
                //$filePrefix  = 'document_';

                $filePath = $this->getUploadDir(self::UPLOAD_PATH, true);

                if ($uploadedFile) {
                    //dd('');
                    $fichier = $utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFile, self::UPLOAD_PATH);

                    //dd($fichier);
                    if ($fichier) {
                        $proprietaire->setPhoto($fichier);
                    }
                }

                if ($uploadedFileRecto) {

                    $fichierRecto = $utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFileRecto, self::UPLOAD_PATH);

                    if ($fichierRecto) {

                        $proprietaire->setRecto($fichierRecto);
                    }
                }
                if ($uploadedFileVerso) {

                    $fichierVerso = $utils->sauvegardeFichier($filePath, $filePrefix, $uploadedFileVerso, self::UPLOAD_PATH);

                    if ($fichierVerso) {

                        $proprietaire->setVerso($fichierVerso);
                    }
                }


                // On sauvegarde en base

                $proprietaireRepository->add($proprietaire, true);

                // On retourne la confirmation
                $this->setMessage("Operation effectuee avec succes");
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
