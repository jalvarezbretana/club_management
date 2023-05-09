<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Helper\FormErrorsToArray;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ClubController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly ClubRepository $clubRepository, private readonly ValidatorInterface $validator)
    {

    }


    #[Route('/clubs', name: 'club_index', methods: 'GET')]
    public function index(): Response
    {
        $clubs = $this->clubRepository->findAll();

        return $this->json($clubs, 200, [], ['groups' => 'club']);
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
        $form = $this->createForm(ClubType::class);
        var_dump($club);
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


}