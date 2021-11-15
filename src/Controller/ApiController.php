<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Credit;
use App\Entity\Debit;
use App\Entity\Thershold;
use App\Entity\User;
use App\Entity\Balance;
use App\Validators\RequestChecker;

/**
 * ApiController - api endpoints
 * @package  Spendings
 * @author   Piotr Rybinski
 */
class ApiController extends AbstractController
{
    /** @var RequestChecker $requestChecker */
    private $requestChecker;
    /** @var EntityManagerInterface $requestChecker */
    private $entityManager;

    public function __construct(RequestChecker $requestChecker, EntityManagerInterface $entityManager)
    {
        $this->requestChecker = $requestChecker;
        $this->entityManager = $entityManager;
    }

    #[Route('/threshold', name: 'threshold', methods: ['POST'])]
    /**
     * @param request
     */
    public function threshold(Request $request): Response
    {
        $user_id = $this->requestChecker->checkParam($request, 'user', 'uuid');
        $ammount = $this->requestChecker->checkParam($request, 'threshold', 'number');

        try {
            $userEntity = $this->getUserEntity($user_id);
            $thresholdEntity = $userEntity->getThershold();
            $thresholdEntity = $userEntity->getThershold()->setThershold((string)$ammount);
            $entityManager = $this->entityManager;
            $entityManager->persist($thresholdEntity);
            $entityManager->flush();
        } catch (Exception $exception) {
            throw new HttpException(400, 'Error saving thershold');
        }

        $response = new JsonResponse('Threshold saved', 200);
        return $response;
    }

    #[Route('/debit', name: 'debit', methods: ['POST'])]
    /**
     * @param request
     */
    public function debit(Request $request): Response
    {
        $user_id = $this->requestChecker->checkParam($request, 'user', 'uuid');
        $ammount = $this->requestChecker->checkParam($request, 'ammount', 'number');

        /* @internal - set default response message, it will be overwritten later on */
        $message = 'Debit saved';

        try {
            $userEntity = $this->getUserEntity($user_id);
            $entityManager = $this->entityManager;
            $debit = new Debit();
            $debit->setUserId($userEntity);
            $debit->setDebit($ammount);
            $entityManager->persist($debit);
            $entityManager->flush();
            $current_balance = $this->updateBalance($userEntity->getUserId(), "-".$ammount, $ammount, null);

            $thershold = $userEntity->getThershold()->getThershold();
            $total_spendings = $userEntity->getBalance()->getTotalDebit();

            if ($thershold < $ammount) {
                $message = 'User Id: '.$user_id. ' Thershold: '.$thershold. ' Total spendings: '.$total_spendings;
                if(!defined('STDOUT')) define('STDOUT', fopen('overspendinglog.txt', 'a'));
                fwrite(STDOUT, $message. PHP_EOL);
            }

        } catch (Exception $exception) {
            throw new HttpException(400, 'Error saving debit');
        }

        $response = new JsonResponse($message, 200);
        return $response;

    }

    #[Route('/credit', name: 'credit', methods: ['POST'])]
    /**
     * @param request
     */
    public function credit(Request $request): Response
    {
        $user_id = $this->requestChecker->checkParam($request, 'user', 'uuid');
        $refund = $this->requestChecker->checkParam($request, 'refund', 'number');

        try {
            $userEntity = $this->getUserEntity($user_id);
            $entityManager = $this->entityManager;
            $credit = new Credit();
            $credit->setUserId($userEntity);
            $credit->setCredit($refund);
            $entityManager->persist($credit);
            $entityManager->flush();
            $this->updateBalance($userEntity->getUserId(), $refund, null, $refund);
        } catch (Exception $exception) {
            throw new HttpException(400, 'Error saving credit');
        }

        $response = new JsonResponse('Credit saved', 200);
        return $response;
    }

    /**
     * Check if user exist in db already and create entry if not
     * @param $user_id
     * @return String
     */
    private function getUserEntity(String $user_id): ?User 
    {
        $entityManager = $this->entityManager;
        $user = $entityManager->getRepository(User::class)->findOneBy(['user_id' => $user_id]);

        if (!isset($user)) {
            try {
                $user = new User();
                $user->setUserId($user_id);
                $entityManager->persist($user);

                $balance = new Balance();
                $balance->setUserId($user);
                $balance->setBalance('0');
                $balance->setTotalDebit('0');
                $balance->setTotalCredit('0');
                $entityManager->persist($balance);

                $thershold = new Thershold();
                $thershold->setUserId($user);
                $thershold->setThershold('0');
                $entityManager->persist($thershold);

                $entityManager->flush();
                
            } catch (Exception $exception) {
                throw new HttpException(400, 'Error saving User');
            }
        }

        return $user;
    }

    /**
     * Update balance
     * @param String||Null $user
     * @param String||Null $balance
     * @param String||Null $debit
     * @param String||Null $credit
     * @return Balance
     */
    private function updateBalance(String $user_id, ?String $balance, ?String $debit, ?String $credit): ?Balance 
    {
        try {
            $entityManager = $this->entityManager;
            $userEntity = $this->getUserEntity($user_id);
            /* @internal Get current values */
            $current_balance = $userEntity->getBalance()->getBalance();
            $current_debit = $userEntity->getBalance()->getTotalDebit();
            $current_credit = $userEntity->getBalance()->getTotalCredit();
            $new_balance = $current_balance + $balance;
            $new_debit = $current_debit + $debit;
            $new_credit = $current_credit + $credit;
            /* @internal Set new values */
            $balanceEntity = $userEntity->getBalance()->setBalance((string)$new_balance);
            $balanceEntity = $userEntity->getBalance()->setTotalDebit((string)$new_debit);
            $balanceEntity = $userEntity->getBalance()->setTotalCredit((string)$new_credit);
            $entityManager->persist($balanceEntity);
            $entityManager->flush();

        } catch (Exception $exception) {
            throw new HttpException(400, 'Error updating Balance');
        }

        return $balanceEntity;
    }

}
