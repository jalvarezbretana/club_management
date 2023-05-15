<?php

namespace App\Controller;


use App\Entity\Trainer;
use App\Form\TrainerType;
use App\Helper\FormErrorsToArray;
use App\Repository\TrainerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrainerController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly TrainerRepository $trainerRepository, private readonly ValidatorInterface $validator)
    {

    }

    #[Route('/trainer', name: 'trainer_index', methods: 'GET')]
    public function index(): Response
    {
        $trainers = $this->trainerRepository->findAll();

        return $this->json($trainers, 200, [], ['groups' => 'trainers']);

    }


    #[Route('/trainer', name: 'trainer_create', methods: 'POST')]
    public function create(Request $request, TrainerRepository $trainerRepository): Response
    {
        $trainers = new Trainer();
        $form = $this->createForm(TrainerType::class, $trainers);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trainerRepository->save($trainers, true);
            return new JsonResponse(['message' => 'Trainer created successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/trainer/{id}', name: 'trainer_show', methods: 'GET')]
    public function show(Trainer $trainers): Response
    {
        return $this->json($trainers, 201, [], ['groups' => 'club']);

    }


    #[Route('/trainer/{id}', name: 'trainer_update', methods: 'PUT')]
    public function update(Request $request, Trainer $trainers, TrainerRepository $trainerRepository): Response
    {
        $form = $this->createForm(TrainerType::class, $trainers, ["method" => "PUT"]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trainers = $form->getData();
            $trainerRepository->save($trainers, true);
            return new JsonResponse(['message' => 'Trainer edited successfully'], Response::HTTP_CREATED);
        }
        return new JsonResponse(['errors' => FormErrorsToArray::staticParseErrorsToArray($form)], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/trainer/{id}', name: 'trainers_delete', methods: 'DELETE')]
    public function delete(Trainer $trainers, TrainerRepository $trainerRepository): Response
    {
        $trainerRepository->remove($trainers, true);

        return $this->json(["Trainer deleted successfully"]);
    }

}