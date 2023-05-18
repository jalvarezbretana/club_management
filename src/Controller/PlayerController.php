<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Helper\FormErrorsToArray;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use App\Repository\TrainerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    public function __construct(private readonly ClubRepository $clubRepository, private readonly PlayerRepository $playerRepository, private readonly TrainerRepository $trainerRepository)
    {

    }

    #[Route('/player', name: 'player_index', methods: 'GET')]
    public function index(): Response
    {
        $players = $this->playerRepository->findAll();

        $data = [];
        foreach ($players as $player) {
            $id = $player->getId();
            $name = $player->getName();
            $surname = $player->getSurname();
            $email = $player->getEmail();
            $dni = $player->getDni();
            $salary = $player->getSalary();
            $phone = $player->getPhone();
            $club = $player->getClub();
            $clubId = $club ? $club->getId() : null;
            $clubName = $club ? $club->getName() : null;

            $data[] = [
                'id' => $id,
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'dni' => $dni,
                'salary' => $salary,
                'phone' => $phone,
                'club_id' => $clubId,
                'club' => $clubName,

            ];
        }

        return $this->json(["PLAYERS" => $data]);

    }


    #[Route('/player', name: 'player_create', methods: 'POST')]
    public function create(Request $request, PlayerRepository $playerRepository): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playerRepository->save($player, true);
            return new JsonResponse(['message' => 'Player created successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/player/{id}', name: 'player_show', methods: 'GET')]
    public function show(Player $player): Response
    {
        $club = $player->getClub();
        $clubId = $club ? $club->getId() : null;
        $clubName = $club ? $club->getName() : null;

        return $this->json([
            "name" => $player->getName(),
            "surname" => $player->getSurname(),
            "email" => $player->getEmail(),
            "dni" => $player->getDni(),
            "salary" => $player->getSalary(),
            "club_id" => $clubId,
            "club" => $clubName]);
    }


    #[Route('/player/{id}', name: 'player_update', methods: 'PUT')]
    public function update(Request $request, Player $player, PlayerRepository $playerRepository): Response
    {
        $form = $this->createForm(PlayerType::class, $player, ["method" => "PUT"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player = $form->getData();
            $playerRepository->save($player, true);
            return new JsonResponse(['message' => 'Player edited successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }

    #[Route('/player/{id}', name: 'player_delete', methods: 'DELETE')]
    public function delete(Player $player, PlayerRepository $playerRepository): Response
    {
        $playerRepository->remove($player, true);
        return $this->json(["Player deleted successfully"]);
    }

}