<?php

namespace App\Tests\Controller;

use App\Controller\UsersController;
use App\Entity\Users;
use App\Repository\UsersRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Flex\Response;

class UsersControllerUnitTest extends TestCase
{
    private $usersRepository;
    private $usersController;

    public function setUp(): void
    {
        parent::setUp();
        $this->usersRepository = $this->getMockBuilder(UsersRepository::class)->disableOriginalConstructor()->getMock();
        $this->usersController = new UsersController();
    }

    public function testIndex()
    {
        $usersRepository = $this->getMockBuilder(UsersRepository::class)->disableOriginalConstructor()->getMock();
        $usersController = new UsersController();

        $user1 = new Users();
        $user1->setName("Anna");
        $user1->setEmail("anna@gmail.com");
        $user2 = new Users();
        $user2->setName("Jordi@gmail.com");
        $user2->setEmail("jordi@gmail.com");
        $arrayUsers = array($user1,$user2);

        $json = json_encode([
            'message' => 'The request has been successfully completed',
            'users' => $arrayUsers,
            'count' => count($arrayUsers)
        ]);

        $this->usersRepository->expects(self::exactly(1))
            ->method('findAll')
            ->willReturn($arrayUsers);


        $result  = $usersController->index($usersRepository);

        $response = new Response($json);

        $this->assertEquals($response,$result);

    }
}