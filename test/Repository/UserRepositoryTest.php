<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Repository;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    public function setUp(): void 
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess() 
    {
        $user = new User();
        $user->id = "Restu";
        $user->name = "Restu";
        $user->password = "Restu";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        Assert::assertEquals($user->id, $result->id);
        Assert::assertEquals($user->name, $result->name);
        Assert::assertEquals($user->password, $result->password);
    }

    public function testFindByIdNotFound() {
        $user = $this->userRepository->findById("Not Found!");
        Assert::assertNull($user);
    }

    public function testUpdate() {
        $user = new User();
        $user->id = "Restu";
        $user->name = "Restu";
        $user->password = "Restu";

        $this->userRepository->save($user);

        $user->id = "Restu";

        $result = $this->userRepository->findById($user->id);

        Assert::assertEquals($user->id, $result->id);
        Assert::assertEquals($user->name, $result->name);
        Assert::assertEquals($user->password, $result->password);
    }
}