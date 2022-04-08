<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="app_users_index", methods={"GET"})
     */
    public function index(UsersRepository $usersRepository): Response
    {

        $users = $usersRepository->findAll();
        $usersAsArray = [];

        foreach ($users as $user) {
            $usersAsArray[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ];
        }

        return $this->json([
            'message' => 'The request has been successfully completed',
            'users' => $usersAsArray,
            'count' => !empty($usersAsArray) ? count($usersAsArray) : 0
        ]);

    }

    /**
     * @Route("/new", name="app_users_new", methods={"POST"})
     */
    function new (EntityManagerInterface $em,Request $request, ValidatorInterface $validator,ManagerRegistry  $doctrine): Response {
        $user = new Users();

        $user->setName($request->get('name'));
        $userFound = $doctrine->getRepository(Users::class)->find($request->get('email'));

        if($userFound){
            return $this->json([
                'message' => 'Email is already in use',
            ]);
        }

        $user->setEmail($request->get('email'));
        $em->persist($user);

        $errors = $validator->validate($user);

        if(count($errors) > 0){
            return $this->json([
                'message' => $errors[0]->getMessage(),
            ]);
        }

        $em->flush();

        return $this->json([
            'message' => 'The request has been successfully completed',
            'user' => $user,
        ]);
    }


}
