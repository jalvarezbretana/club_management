<?php

namespace App\Controller;

use App\Entity\Players;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly PlayerRepository $playerRepository)
    {

    }

    #[Route('/players', name: 'player_index', methods: 'GET')]
    public function index(): Response
    {
        $players = $this->playerRepository->findAll();

        return $this->json($players, 200, [], ['groups' => 'players']);

    }


    #[Route('/players', name: 'player_create', methods: 'POST')]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $player = new Players();
        $player->setName($data['name'] ?? 'Default name')
            ->setSurname($data['surname'] ?? 'Default surname')
            ->setDni($data['dni'] ?? '0000000A')
            ->setEmail($data['email'] ?? 'Default email')
            ->setSalary(rand(1100, 1800)
            );
        // Set other properties of the player

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $this->json($player, 201, [], ['groups' => 'player']);
//        return new Response(sprintf(
//            'Id: %s, Player name:%s, %s, DNI: %s,  Email: %s, Salary: %d',
//            $player->getId(),
//            $player->getName(),
//            $player->getSurname(),
//            $player->getDNI(),
//            $player->getEmail(),
//            $player->getSalary()
//        ));
    }


    #[Route('/players/{id}', name: 'player_show', methods: 'GET')]
    public function show(Players $player): Response
    {
        return $this->json($player, 201, [], ['groups' => 'player']);

//        return new Response(sprintf(
//            'Id: %s, Player name:%s, %s, DNI: %s,  Email: %s, Salary: %d',
//            $player->getId(),
//            $player->getName(),
//            $player->getSurname(),
//            $player->getDNI(),
//            $player->getEmail(),
//            $player->getSalary()
//        ));
    }


    #[Route('/players/{id}', name: 'player_update', methods: 'PUT')]
    public function update(Request $request, Players $player): Response
    {
        $data = json_decode($request->getContent(), true);

        $player->setName($data['name'] ?? 'Victor')
            ->setSurname($data['surname'] ?? 'Arana')
            ->setDni($data['dni'] ?? '0000000A')
            ->setEmail($data['email'] ?? 'victor@email.com')
            ->setSalary(rand(1100, 1800)
            );
        // Update other properties of the club

        $this->entityManager->flush();
        return $this->json($player, 201, [], ['groups' => 'player']);

//        return new Response(sprintf(
//            'Id: %s, Player name:%s, %s, DNI: %s,  Email: %s, Salary: %d',
//            $player->getId(),
//            $player->getName(),
//            $player->getSurname(),
//            $player->getDNI(),
//            $player->getEmail(),
//            $player->getSalary()
//        ));
    }


    #[Route('/players/{id}', name: 'player_delete', methods: 'DELETE')]
    public function delete(Players $player): Response
    {
        $this->entityManager->remove($player);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

}