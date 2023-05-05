<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Repository\ClubRepository;
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
        $clubs = $this->clubRepository->findAll();

        return $this->json($clubs, 200, [], ['groups' => 'club']);
    }


    #[Route('/clubs', name: 'club_create', methods: 'POST')]
    public function createClub(Request $request): JsonResponse
    {
        $clubData = json_decode($request->getContent(), true);

        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);

        $form->submit($clubData);

        $errors = $this->validator->validate($club);

        if ($form->isValid() && count($errors) === 0) {
            // Handle the form submission and persist the club entity
            $this->entityManager->persist($club);
            $this->entityManager->flush();

            // Return a success JSON response
            return new JsonResponse(['message' => 'Club created successfully'], Response::HTTP_CREATED);
        }

        // Return a JSON response with the form errors
        $formErrors = $this->getFormErrors($form);
        $validationErrors = $this->getValidationErrors($errors);
        $errors = array_merge($formErrors, $validationErrors);

        return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    private function getFormErrors($form)
    {
        $errors = [];

        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }

    private function getValidationErrors($errors)
    {
        $validationErrors = [];

        foreach ($errors as $error) {
            $validationErrors[] = $error->getMessage();
        }

        return $validationErrors;
    }


    #[Route('/clubs/{id}', name: 'club_show', methods: 'GET')]
    public function show(Club $club): Response
    {
        return $this->json($club, 201, [], ['groups' => 'club']);

    }


    #[Route('/clubs/{id}', name: 'club_update', methods: 'PUT')]
    public function update(Request $request, Club $club): Response
    {
        $data = json_decode($request->getContent(), true);

        $club->setName('Victor');
        $club->setEmail('Victor@gmail.com');
        $club->setBudget(rand(20000, 30000));
        // Update other properties of the club

        $this->entityManager->flush();
        return $this->json($club, 201, [], ['groups' => 'club']);

//        return new Response(sprintf(
//            'Id: %s, Club name:%s, Email: %s, Budget: %d',
//            $club->getId(),
//            $club->getName(),
//            $club->getEmail(),
//            $club->getBudget()
//        ));
    }

    #[Route('/clubs/{id}', name: 'club_delete', methods: 'DELETE')]
    public function delete(Club $club): Response
    {
        $this->entityManager->remove($club);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }


}