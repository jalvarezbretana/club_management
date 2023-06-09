<?php

namespace App\Controller;


use App\Entity\Trainer;
use App\Form\TrainerType;
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

class TrainerController extends AbstractController
{
    public function __construct(private readonly ClubRepository $clubRepository, private readonly PlayerRepository $playerRepository, private readonly TrainerRepository $trainerRepository)
    {

    }

    #[Route('/trainer', name: 'trainer_index', methods: 'GET')]
//    #[OA\Get(path: '/trainer', tags: ['Trainer CRUD'])]
    public function index(): Response
    {
        $trainers = $this->trainerRepository->findAll();

        $data = [];
        foreach ($trainers as $trainer) {
            $id = $trainer->getId();
            $name = $trainer->getName();
            $surname = $trainer->getSurname();
            $email = $trainer->getEmail();
            $dni = $trainer->getDni();
            $salary = $trainer->getSalary();
            $phone = $trainer->getPhone();
            $club = $trainer->getClub();
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

        return $this->json(["TRAINERS" => $data]);
    }


    #[Route('/trainer', name: 'trainer_create', methods: 'POST')]
//    #[OA\Post(path: '/trainer', tags: ['Trainer CRUD'])]
    public function create(Request $request): Response
    {
        $trainer = new Trainer();
        $form = $this->createForm(TrainerType::class, $trainer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->trainerRepository->save($trainer, true);
            return new JsonResponse(['message' => 'Trainer created successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/trainer/{id}', name: 'trainer_show', methods: 'GET')]
//    #[OA\Get(path: '/trainer/{id}', tags: ['Trainer CRUD'])]
    public function show(Trainer $trainer): Response
    {
        $club = $trainer->getClub();
        $clubId = $club?->getId();
        $clubName = $club?->getName();

        return $this->json([
            "name" => $trainer->getName(),
            "surname" => $trainer->getSurname(),
            "email" => $trainer->getEmail(),
            "dni" => $trainer->getDni(),
            "salary" => $trainer->getSalary(),
            "club_id" => $clubId,
            "club" => $clubName]);
    }

    #[Route('/trainer/{id}', name: 'trainer_update', methods: 'PUT')]
//    #[OA\Put(path: '/trainer/{id}', tags: ['Trainer CRUD'])]
    public function update(Request $request, Trainer $trainer): Response
    {
        $form = $this->createForm(TrainerType::class, $trainer, ["method" => "PUT"]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trainer = $form->getData();
            $this->trainerRepository->save($trainer, true);
            return new JsonResponse(['message' => 'Trainer updated successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);

    }


    #[Route('/trainer/{id}', name: 'trainers_delete', methods: 'DELETE')]
//    #[OA\Delete(path: '/trainer/{id}', tags: ['Trainer CRUD'])]
    public function delete(Trainer $trainer): Response
    {
        $this->trainerRepository->remove($trainer, true);
        return $this->json(["Trainer deleted successfully"]);
    }

}