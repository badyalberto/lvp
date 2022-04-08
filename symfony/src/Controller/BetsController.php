<?php

namespace App\Controller;

use App\Entity\Bets;
use App\Entity\Users;
use App\Repository\BetsRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/bets")
 */
class BetsController extends AbstractController
{

    const killsWinners = 12;
    /**
     * @Route("/", name="app_bets_index", methods={"GET"})
     */
    public function index(BetsRepository $betsRepository): Response
    {
        $bets = $betsRepository->findAll();
        $betsAsArray = [];

        foreach ($bets as $bet) {
            $betsAsArray[] = [
                'id' => $bet->getId(),
                'email' => $bet->getIdUser()->getEmail(),
                'kills' => $bet->getKills(),
            ];
        }

        return $this->json([
            'message' => 'The request has been successfully completed',
            'bets' => $betsAsArray,
            'count' => count($betsAsArray),
        ]);
    }

    /**
     * @Route("/new", name="app_bets_new", methods={"POST"})
     */
    function new (EntityManagerInterface $em, Request $request, ManagerRegistry $doctrine,ValidatorInterface $validator): Response {
        $bet = new Bets();

        $user = $doctrine->getRepository(Users::class)->find($request->get('user_id'));

        if(!is_string($request->get('kills'))){
            $bet->setKills($request->get('kills'));
        }

        if (!$user) {
            return $this->json([
                'message' => 'User dont\'t exists!',
            ]);
        }
        $bet->setIdUser($user);


        $errors = $validator->validate($bet);
        if(count($errors) > 0){
            return $this->json([
                'message' => $errors[0]->getMessage(),
            ]);
        }
        $em->persist($bet);
        $em->flush();

        return $this->json([
            'message' => 'The request has been successfully completed',
            'bet' => $bet,
        ]);
    }

    /**
     * @Route("/statistics", name="app_bets_statistics", methods={"GET"})
     */
    public function statistics(EntityManagerInterface $em, BetsRepository $betsRepository): Response
    {
        $bets = $betsRepository->findAll();
        $queryMax = $em->createQuery('SELECT MAX(b.kills) FROM App\Entity\Bets b');
        $max = $queryMax->getSingleResult();
        $queryMin = $em->createQuery('SELECT MIN(b.kills) FROM App\Entity\Bets b');
        $min = $queryMin->getSingleResult();

        $queryAvg = $em->createQuery('SELECT AVG(b.kills) FROM App\Entity\Bets b');
        $avg = $queryAvg->getSingleResult();

        $betsAsArray = [];

        foreach ($bets as $bet) {
            $betsAsArray[] = [
                'id' => $bet->getId(),
                'email' => $bet->getIdUser()->getEmail(),
                'kills' => $bet->getKills(),
            ];
        }

        return $this->json([
            'total_bets' => count($betsAsArray),
            'max_kills' =>  $max,
            'min_kills' =>  $min,
            'average_kills' => $avg,

        ]);
    }

    /**
     * @Route("/winner", name="app_bets_winner", methods={"GET"})
     */
    public function winner(EntityManagerInterface $em, BetsRepository $betsRepository): Response
    {
        $bets = $betsRepository->findAll();

        $winner = null;
        $kills = 0;
        foreach ($bets as $bet) {
           if($bet->getKills() <= self::killsWinners && $bet->getKills() >= $kills ){
                $winner = $bet->getIdUser()->getEmail();
                $kills = $bet->getKills();
           }
        }
        if(!$bets){
            return $this->json([
                'winner' => "There are no bets!"
            ]);
        }

        return $this->json([
            'winner' => $winner
        ]);
    }

}
