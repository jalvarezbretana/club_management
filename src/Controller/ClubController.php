<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Player;
use App\Entity\Trainer;
use App\Form\ClubBudgetType;
use App\Form\ClubType;
use App\Form\PlayerType;
use App\Form\TrainerType;
use App\Helper\FormErrorsToArray;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use App\Repository\TrainerRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;


class ClubController extends AbstractController
{
    public function __construct(private readonly ClubRepository    $clubRepository, private readonly PlayerRepository $playerRepository,
                                private readonly TrainerRepository $trainerRepository, private readonly MailerInterface $mailer)
    {

    }


    #[Route('/club', name: 'club_index', methods: 'GET')]
//    #[OA\Get(path: '/club', tags: ['Club CRUD'])]
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
                $playerSalary = $player->getSalary();
                $playerData[] = [
                    'id' => $playerId,
                    'name' => $playerName,
                    'salary' => $playerSalary
                ];
            }
            foreach ($trainers as $trainer) {
                $trainerId = $trainer->getId();
                $trainerName = $trainer->getName();
                $trainerSalary = $trainer->getSalary();
                $trainerData[] = [
                    'id' => $trainerId,
                    'name' => $trainerName,
                    'salary' => $trainerSalary,
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
//    #[OA\Post(path: '/club', tags: ['Club CRUD'])]
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
//    #[OA\Get(path: '/club/{id}', tags: ['Club CRUD'])]
    public function show_club(Club $club): Response
    {
        return $this->json([
            "name" => $club->getName(),
            "total_budget" => $club->getBudget(),
            "remaining_budget" => $club->getAvailableBudget()]);
    }

    #[Route('/club/{id}', name: 'club_update', methods: 'PUT')]
//    #[OA\Put(path: '/club/{id}', tags: ['Club CRUD'])]
    public function update_club(Request $request, Club $club): Response
    {
        $form = $this->createForm(ClubType::class, $club, ["method" => "PUT"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();
            $this->clubRepository->save($club, true);
            return new JsonResponse(['message' => 'Club updated successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/club/{id}', name: 'club_update_budget', methods: 'PATCH')]
//    #[OA\Patch(path: '/club/{id}', tags: ['Club CRUD'])]
    public function update_budget(Request $request, Club $club): Response
    {
        $form = $this->createForm(ClubBudgetType::class, $club, ["method" => "PATCH"]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $club = $form->getData();
            $this->clubRepository->save($club, true);
            return new JsonResponse(['message' => 'Club budget updated successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/club/{id}', name: 'club_delete', methods: 'DELETE')]
//    #[OA\Delete(path: '/club/{id}', tags: ['Club CRUD'])]
    public function delete_club(Club $club): Response
    {
        $this->clubRepository->remove($club, true);
        return $this->json(["Club deleted successfully"]);
    }

    #[Route('/club/{id}/player', name: 'club_create_player', methods: 'POST')]
//    #[OA\Post(path: '/club/{id}/player', tags: ['Club CRUD'])]
    public function club_create_player(Request $request, Club $club): Response
    {
        $player = new Player();
        $player->setClub($club);
        $form = $this->createForm(PlayerType::class, $player, ["method" => "POST", "club" => $club]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $player = $form->getData();
            $this->playerRepository->save($player, true);
            $pEmail = $player->getEmail();
            $email = (new Email())
                ->from('hello@example.com')
                ->to($pEmail)
                ->subject('Player CREATED')
                ->text('You have been registered in the club.');
            $this->mailer->send($email);
            return new JsonResponse(['message' => 'Player created in club successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }
//    #[Route('/club/{id}/player/{player_id}', name: 'club_delete_player', methods: ['DELETE'])]
//    #[ParamConverter('player', options:[ 'mapping' =>["player_id"=> "id"], ['exclude'=>["id"]]])]
//    #[ParamConverter('club', options: ['mapping'=>["id"=>"id"], ['exclude'=>["player_id"]]])]
//    public function club_delete_player(Club $club, Player $player): Response
//    {
//        $this->playerRepository->remove($player, true);
//        $pEmail = $player->getEmail();
//        $email = (new Email())
//            ->from('hello@example.com')
//            ->to($pEmail)
//            ->subject('Player DELETED')
//            ->text('You have been dropped from the club.');
//        $this->mailer->send($email);
//        return $this->json(['message'=>'Player deleted successfully']);
//
//    }
    #[Route('/club/{id}/trainer', name: 'club_create_trainer', methods: 'POST')]
//    #[OA\Post(path: '/club/{id}/trainer', tags: ['Club CRUD'])]
    public function club_create_trainer(Request $request, Club $club): Response
    {
        $trainer = new Trainer();
        $trainer->setClub($club);
        $form = $this->createForm(TrainerType::class, $trainer, ["method" => "POST", "club" => $club]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trainer = $form->getData();
            $this->trainerRepository->save($trainer, true);
            $tEmail = $trainer->getEmail();
            $email = (new Email())
                ->from('hello@example.com')
                ->to($tEmail)
                ->subject('Trainer CREATED')
                ->text('You have been registered in the club.');
            $this->mailer->send($email);
            return new JsonResponse(['message' => 'Trainer created in club successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }
//
//    #[Route('/club/{id}/trainer/{trainer_id}', name: 'club_delete_trainer', methods: 'DELETE')]
//    #[ParamConverter('club', options: ['mapping' => ['id' => 'id'], 'exclude' => ['trainer_id']])]
//    #[ParamConverter('trainer', options: ['mapping' => ['trainer_id' => 'id'], 'exclude' => ['id']])]
//    public function club_delete_trainer(Club $club, Trainer $trainer): Response
//    {
//        $this->trainerRepository->remove($trainer, true);
//        $tEmail = $trainer->getEmail();
//        $email = (new Email())
//            ->from('hello@example.com')
//            ->to($tEmail)
//            ->subject('Trainer DELETED')
//            ->text('You have been dropped from the club.');
//        $this->mailer->send($email);
//        return $this->json(['message' => 'Trainer deleted successfully']);
//
//    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/club/{id}/player', name: 'club_list_player', methods: 'GET')]
//    #[OA\Get(path: '/club/{id}/player', tags: ['Club CRUD'])]
    public function club_list_players(Request $request, Club $club): Response
    {
        $club = $this->clubRepository->find($club->getId());

        $nameFilter = $request->query->get('name');
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', 10);

        if (!$club) {
            return new JsonResponse(['error' => 'Club not found.'], 404);
        }
        //qb=QueryBuilder
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
//            'per_page' => $perPage,
            'total_players' => $totalPlayers,
        ];
        foreach ($players as $player) {
            $data['players'][] = [
//                'id' => $player->getId(),
                'name' => $player->getName(),
            ];

        }
        return $this->json([$data]);
    }
}