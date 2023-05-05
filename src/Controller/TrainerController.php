<?php

namespace App\Controller;


use App\Entity\Trainers;
use App\Repository\TrainerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrainerController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly TrainerRepository $trainerRepository)
    {

    }

    #[Route('/trainers', name: 'trainer_index', methods: 'GET')]
    public function index(): Response
    {
        $trainers = $this->trainerRepository->findAll();

        return $this->json($trainers, 200, [], ['groups' => 'trainers']);

    }


    #[Route('/trainers', name: 'trainer_create', methods: 'POST')]
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $trainer = new Trainers();
        $trainer->setName($data['name'] ?? 'Default name')
            ->setSurname($data['surname'] ?? 'Default surname')
            ->setDni($data['dni'] ?? '0000000A')
            ->setEmail($data['email'] ?? 'Default email')
            ->setSalary(rand(1100, 1800)
            );
        // Set other properties of the trainer

        $this->entityManager->persist($trainer);
        $this->entityManager->flush();

        return $this->json($trainer, 201, [], ['groups' => 'trainer']);
//        return new Response(sprintf(
//            'Id: %s, Player name:%s, %s, DNI: %s,  Email: %s, Salary: %d',
//            $trainer->getId(),
//            $trainer->getName(),
//            $trainer->getSurname(),
//            $trainer->getDNI(),
//            $trainer->getEmail(),
//            $trainer->getSalary()
//        ));
    }


    #[Route('/trainers/{id}', name: 'trainer_show', methods: 'GET')]
    public function show(Trainers $trainer): Response
    {
        return $this->json($trainer, 201, [], ['groups' => 'trainer']);

//        return new Response(sprintf(
//            'Id: %s, Player name:%s, %s, DNI: %s,  Email: %s, Salary: %d',
//            $trainer->getId(),
//            $trainer->getName(),
//            $trainer->getSurname(),
//            $trainer->getDNI(),
//            $trainer->getEmail(),
//            $trainer->getSalary()
//        ));
    }


    #[Route('/trainers/{id}', name: 'trainer_update', methods: 'PUT')]
    public function update(Request $request, Trainers $trainer): Response
    {
        $data = json_decode($request->getContent(), true);

        $trainer->setName($data['name'] ?? 'Victor')
            ->setSurname($data['surname'] ?? 'Arana')
            ->setDni($data['dni'] ?? '0000000A')
            ->setEmail($data['email'] ?? 'victor@email.com')
            ->setSalary(rand(1100, 1800)
            );
        // Update other properties of the club

        $this->entityManager->flush();
        return $this->json($trainer, 201, [], ['groups' => 'trainer']);

//        return new Response(sprintf(
//            'Id: %s, Player name:%s, %s, DNI: %s,  Email: %s, Salary: %d',
//            $trainer->getId(),
//            $trainer->getName(),
//            $trainer->getSurname(),
//            $trainer->getDNI(),
//            $trainer->getEmail(),
//            $trainer->getSalary()
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