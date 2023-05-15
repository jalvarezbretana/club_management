<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Helper\FormErrorsToArray;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlayerController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly PlayerRepository $playerRepository, private readonly ValidatorInterface $validator)
    {

    }

    #[Route('/player', name: 'player_index', methods: 'GET')]
    public function index(): Response
    {
        $players = $this->playerRepository->findAll();

        return $this->json($players, 200, [], ['groups' => 'players']);

    }


    #[Route('/player', name: 'player_create', methods: 'POST')]
    public function create(Request $request, PlayerRepository $playerRepository): Response
    {
        $players = new Player();
        $form = $this->createForm(PlayerType::class, $players);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $playerRepository->save($players, true);
            return new JsonResponse(['message' => 'Player created successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/player{id}', name: 'player_show', methods: 'GET')]
    public function show(Player $players): Response
    {
        return $this->json($players, 201, [], ['groups' => 'club']);

    }


    #[Route('/player/{id}', name: 'player_update', methods: 'PUT')]
    public function update(Request $request, Player $players, PlayerRepository $playerRepository): Response
    {
        $form = $this->createForm(PlayerType::class, $players, ["method" => "PUT"]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $players = $form->getData();
            $playerRepository->save($players, true);
            return new JsonResponse(['message' => 'Player edited successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/player/{id}', name: 'player_delete', methods: 'DELETE')]
    public function delete(Player $players, PlayerRepository $playerRepository): Response
    {
        $playerRepository->remove($players, true);
        return $this->json(["Player deleted successfully"]);

    }

}