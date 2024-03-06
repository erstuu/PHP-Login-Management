<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

require_once __DIR__ . "/../Helper/Helper.php";

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    public function setUp() : void {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "Restu";
        $user->name = "restu";
        $user->password = "rahasia";

        $this->userRepository->save($user);
    }

    public function testCreate() {
        $session = $this->sessionService->create("Restu");

        TestCase::expectOutputRegex("[X-PZN-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);
        TestCase::assertEquals("Restu", $result->userId);
    }

    public function testDestroy() {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "Restu";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-PZN-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent() {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "Restu";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->userRepository->findById($session->userId);

        TestCase::assertEquals($session->userId, $user->id);
    }
}