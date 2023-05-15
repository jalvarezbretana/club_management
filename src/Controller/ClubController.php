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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//use PhpParser\Comment;


class ClubController extends AbstractController
{
    public function __construct(private readonly ClubRepository $clubRepository)
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
            $availableBudget = $club->getAvailableBudget();

            $data[] = [
                'id' => $id,
                'name' => $name,
                'total_budget' => $budget,
                'remaining_budget' => $availableBudget,
            ];
        }

        return $this->json(["CLUBS" => $data]);

    }

    #[Route('/club', name: 'club_create', methods: 'POST')]
    public function create_club(Request $request, ClubRepository $clubRepository): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clubRepository->save($club, true);
            return new JsonResponse(['message' => 'Club created successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/club/{id}', name: 'club_show', methods: 'GET')]
    public function show_club(Club $club): Response
    {
        return $this->json(["remaining_budget" => $club->getAvailableBudget()]);

    }

    #[Route('/club/{id}', name: 'club_update', methods: 'PUT')]
    public function update_club(Request $request, Club $club, ClubRepository $clubRepository): Response
    {
        $form = $this->createForm(ClubType::class, $club, ["method" => "PUT"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();
            $clubRepository->save($club, true);
            return new JsonResponse(['message' => 'Club edited successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/club/{id}', name: 'club_delete', methods: 'DELETE')]
    public function delete_club(Club $club, ClubRepository $clubRepository): Response
    {
        $clubRepository->remove($club, true);
        return $this->json(["Club deleted successfully"]);
    }

    #[Route('/club/{id}/player', name: 'club_create_player', methods: 'POST')]
    public function club_create_player(Request $request, Club $club, PlayerRepository $playerRepository): Response
    {
        $player = new Player();
        $player->setClub($club);
        $form = $this->createForm(PlayerType::class, $player, ["method" => "POST", "club" => $club]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player = $form->getData();
            $playerRepository->save($player, true);
            return new JsonResponse(['message' => 'Player created in club successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }

    #[Route('/club/{id}/trainer', name: 'club_create_trainer', methods: 'POST')]
    public function club_create_trainer(Request $request, Club $club, TrainerRepository $trainerRepository): Response
    {
        $trainer = new Trainer();
        $trainer->setClub($club);
        $form = $this->createForm(TrainerType::class, $trainer, ["method" => "POST", "club" => $club]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trainer = $form->getData();
            $trainerRepository->save($trainer, true);
            return new JsonResponse(['message' => 'Trainer created in club successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }

//    #[Route('/club/{id}/player/{player_id}', name: 'club_delete_player', methods: 'DELETE')]
//    #[Entity('player', expr: 'repository.find(player_id)')]
//    #[ParamConverter('player', options: ['mapping' => ['player_id' => 'id']])]
//    public function club_delete_player(Club $club, Player $player): Response
//    {
//        $club->removePlayer($player);
//        return $this->json([null, 204]);
//
//    }
}