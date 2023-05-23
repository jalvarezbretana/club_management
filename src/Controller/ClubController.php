<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Player;
use App\Entity\Trainer;
use App\Form\ClubType;
use App\Form\PlayerType;
use App\Form\TrainerType;
use App\Helper\FormErrorsToArray;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use App\Repository\TrainerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ClubController extends AbstractController
{
    public function __construct(private readonly ClubRepository $clubRepository, private readonly PlayerRepository $playerRepository, private readonly TrainerRepository $trainerRepository)
    {

    }

    #[Route('/club', name: 'club_index', methods: 'GET')]
    public function index(): Response
    {
        $clubs = $this->clubRepository->findAll();
        $data = [];

        foreach ($clubs as $club) {
            $id = $club->getId();
            $name = $club->getName();
            $budget = $club->getBudget();
            $email = $club->getEmail();
            $phone = $club->getPhone();
            $availableBudget = $club->getAvailableBudget();
            $players = $club->getPlayers();
            $playerData = [];
            $trainers = $club->getTrainers();
            $trainerData = [];

            foreach ($players as $player) {
                $playerId = $player->getId();
                $playerName = $player->getName();
                $playerData[] = [
                    'id' => $playerId,
                    'name' => $playerName,
                ];
            }
            foreach ($trainers as $trainer) {
                $trainerId = $trainer->getId();
                $trainerName = $trainer->getName();
                $trainerData[] = [
                    'id' => $trainerId,
                    'name' => $trainerName,
                ];
            }

            $data[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'total_budget' => $budget,
                'remaining_budget' => $availableBudget,
                'phone' => $phone,
                'players' => $playerData,
                'trainers' => $trainerData,
            ];
        }

        return $this->json(["CLUBS" => $data]);

    }

    #[Route('/club', name: 'club_create', methods: 'POST')]
    public function create_club(Request $request): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->clubRepository->save($club, true);
            return new JsonResponse(['message' => 'Club created successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/club/{id}', name: 'club_show', methods: 'GET')]
    public function show_club(Club $club): Response
    {
        return $this->json(["name" => $club->getName(), "total_budget" => $club->getBudget(), "remaining_budget" => $club->getAvailableBudget()]);

    }

    #[Route('/club/{id}', name: 'club_update', methods: 'PUT')]
    public function update_club(Request $request, Club $club): Response
    {
        $form = $this->createForm(ClubType::class, $club, ["method" => "PUT"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();
            $this->clubRepository->save($club, true);
            return new JsonResponse(['message' => 'Club edited successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/club/{id}', name: 'club_update_budget', methods: 'PATCH')]
    public function update_budget(Request $request, Club $club): Response
    {
        $form = $this->createForm(ClubType::class, $club, ["method" => "PATCH"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();
            $this->clubRepository->save($club, true);
            return new JsonResponse(['message' => 'Budget edited successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/club/{id}', name: 'club_delete', methods: 'DELETE')]
    public function delete_club(Club $club): Response
    {
        $this->clubRepository->remove($club, true);
        return $this->json(["Club deleted successfully"]);
    }

    #[Route('/club/{id}/player', name: 'club_create_player', methods: 'POST')]
    public function club_create_player(Request $request, Club $club): Response
    {
        $player = new Player();
        $player->setClub($club);
        $form = $this->createForm(PlayerType::class, $player, ["method" => "POST", "club" => $club]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player = $form->getData();
            $this->playerRepository->save($player, true);
            return new JsonResponse(['message' => 'Player created in club successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }

    #[Route('/club/{id}/trainer', name: 'club_create_trainer', methods: 'POST')]
    public function club_create_trainer(Request $request, Club $club): Response
    {
        $trainer = new Trainer();
        $trainer->setClub($club);
        $form = $this->createForm(TrainerType::class, $trainer, ["method" => "POST", "club" => $club]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trainer = $form->getData();
            $this->trainerRepository->save($trainer, true);
            return new JsonResponse(['message' => 'Trainer created in club successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }

    #[Route('/club/{id}/players', name: 'club_list_players', methods: 'GET')]
    public function club_list_players(Request $request, Club $club): Response
    {
        $club = $this->clubRepository->find($club->getId());

        $nameFilter = $request->query->get('name');
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', 10);

        if (!$club) {
            return new JsonResponse(['error' => 'Club not found.'], 404);
        }

        $qb = $this->playerRepository->createQueryBuilder('p')
            ->where('p.club = :club')
            ->setParameter('club', $club);

        if ($nameFilter) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $nameFilter . '%');
        }

        $players = $qb->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getResult();

        $totalPlayers = $qb->select('COUNT(p)')
            ->getQuery()
            ->getSingleScalarResult();

        $data = [
            'club' => $club->getName(),
            'players' => [],
            'page' => $page,
            'per_page' => $perPage,
            'total_players' => $totalPlayers,
        ];
        foreach ($players as $player) {
            $data['players'][] = [
                'id' => $player->getId(),
                'name' => $player->getName(),
            ];

        }
        return $this->json([$data]);

    }
//    #[Route('/club/{id}/player/{player_id}', name: 'club_delete_player', methods: ['DELETE'])]
//    #[ParamConverter('player', options:[ 'mapping' =>["player_id"=> "id"], ['exclude'=>["id"]]])]
//    #[ParamConverter('club', options: ['mapping'=>["id"=>"id"], ['exclude'=>["player_id"]]])]
//    public function club_delete_player(Club $club, Player $player): Response
//    {
//        $this->playerRepository->remove($player, true);
//        return $this->json(['message'=>'Player deleted successfully']);
//
//    }
//    #[Route('/club/{id}/trainer/{trainer_id}', name: 'club_delete_trainer', methods: 'DELETE')]
//    #[ParamConverter('club', options: ['mapping' => ['id' => 'id'], 'exclude' => ['trainer_id']])]
//    #[ParamConverter('trainer', options: ['mapping' => ['trainer_id' => 'id'], 'exclude' => ['id']])]
//    public function club_delete_trainer(Club $club, Trainer $trainer): Response
//    {
//        $this->trainerRepository->remove($trainer, true);
//        return $this->json(['message'=>'Trainer deleted successfully']);
//
//    }
}