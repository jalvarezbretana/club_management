<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Players;
use App\Form\ClubType;
use App\Form\PlayerType;
use App\Helper\FormErrorsToArray;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ClubController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly ClubRepository $clubRepository, private readonly ValidatorInterface $validator)
    {

    }


    #[Route('/clubs', name: 'club_index', methods: 'GET')]
    public function index(): Response
    {
        $club = $this->clubRepository->findAll();

        return $this->json($club, 200, [], ['groups' => 'club']);
    }

    #[Route('/clubs', name: 'club_create', methods: 'POST')]
    public function create(Request $request, ClubRepository $clubRepository): Response
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

    #[Route('/clubs/{id}', name: 'club_show', methods: 'GET')]
    public function show(Club $club): Response
    {
        return $this->json($club, 201, [], ['groups' => 'club']);

    }

    #[Route('/clubs/{id}', name: 'club_update', methods: 'PUT')]
    public function update(Request $request, Club $club, ClubRepository $clubRepository): Response
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

    #[Route('/clubs/{id}', name: 'club_delete', methods: 'DELETE')]
    public function delete(Club $club, ClubRepository $clubRepository): Response
    {
        $clubRepository->remove($club, true);

        return $this->json(null, 204);
    }

    #[Route('/clubs/{id}/players', name: 'club_create_player', methods: 'POST')]
    public function club_create_player(Request $request, Club $club, PlayerRepository $playerRepository): Response
    {
        $players = new Players();
        $players->setClub($club);
        $form = $this->createForm(PlayerType::class, $players, ["method" => "POST", "club" => $club]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $players = $form->getData();
            $playerRepository->save($players, true);
            return new JsonResponse(['message' => 'Player created in club successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }

    #[Route('/clubs/budget/{id}', name: 'club_budget', methods: 'GET')]
    public function club_budget(Club $club): Response
    {
        $players = $club->getPlayers();
        $totalSalary = 0;

        foreach ($players as $player) {
            $totalSalary += $player->getSalary();
        }

        $remainingBudget = $club->getBudget() - $totalSalary;

        return new JsonResponse([
            'club_id' => $club->getId(),
            'total_players' => $club->countPlayers(),
            'total_salary' => $totalSalary,
            'remaining_budget' => $remainingBudget,
        ]);
    }


}