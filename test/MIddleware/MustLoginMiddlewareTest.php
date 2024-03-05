<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App 
{
    function header(string $value) {
        echo $value;
    }
}

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Middleware
{
    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;

    class MustLoginMiddlewareTest extends TestCase 
    {
        private MustLoginMiddleware $middleware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        public function setUp(): void 
        {
            $this->middleware = new MustLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBeforeGuest()
        {
            $this->middleware->before();
            TestCase::expectOutputRegex("[Location: /users/login]");
        }

        public function testBeforeLoginUser() {
            $user = new User();
            $user->id = "restu";
            $user->name = "Restu";
            $user->password = "restu1234";

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->middleware->before();
            TestCase::expectOutputString("");
        }
    }   
}