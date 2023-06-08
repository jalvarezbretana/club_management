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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class PlayerController extends AbstractController
{
    public function __construct(private readonly ClubRepository $clubRepository, private readonly PlayerRepository $playerRepository, private readonly TrainerRepository $trainerRepository)
    {

    }

    #[Route('/player', name: 'player_index', methods: 'GET')]
//    #[OA\Get(path: '/player', tags: ['Player CRUD'])]

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
            $clubId = $club?->getId();
            $clubName = $club?->getName();

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
//    #[OA\Post(path: '/player', tags: ['Player CRUD'])]

    public function create(Request $request): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->playerRepository->save($player, true);
            return new JsonResponse(['message' => 'Player created successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/player/{id}', name: 'player_show', methods: 'GET')]
//    #[OA\Get(path: '/player/{id}', tags: ['Player CRUD'])]

    public function show(Player $player): Response
    {
        $club = $player->getClub();
        $clubId = $club?->getId();
        $clubName = $club?->getName();

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
//    #[OA\Put(path: '/player/{id}', tags: ['Player CRUD'])]

    public function update(Request $request, Player $player): Response
    {
        $form = $this->createForm(PlayerType::class, $player, ["method" => "PUT"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player = $form->getData();
            $this->playerRepository->save($player, true);
            return new JsonResponse(['message' => 'Player updated successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }

    #[Route('/player/{id}', name: 'player_delete', methods: 'DELETE')]
//    #[OA\Delete(path: '/player/{id}', tags: ['Player CRUD'])]

    public function delete(Player $player): Response
    {
        $this->playerRepository->remove($player, true);
        return $this->json(["Player deleted successfully"]);
    }

}