<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Repository;

use PDO;
use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;

class SessionRepositoryTest extends TestCase 
{
    private SessionRepository $sessionRepository;

    public function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        
        $this->sessionRepository->deleteAll();
    }

    public function testSaveSuccess() 
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "restu";

        $this->sessionRepository->save($session);

        $result = $this->sessionRepository->findById($session->id);

        TestCase::assertEquals($session->id, $result->id);
        TestCase::assertEquals($session->userId, $result->userId);
    }

    public function testDeleteById() 
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "restu";

        $this->sessionRepository->save($session);    

        $result = $this->sessionRepository->findById($session->id);

        TestCase::assertEquals($session->id, $result->id);
        TestCase::assertEquals($session->userId, $result->userId);

        $this->sessionRepository->deleteById($session->id);

        $result = $this->sessionRepository->findById($session->id);
        TestCase::assertNull($result);
    }

    public function testFindByIdNotFound() 
    {
        $result = $this->sessionRepository->findById("NotFound");
        TestCase::assertNull($result);
    }
}