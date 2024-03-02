<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App {
    function header(string $value) {
        echo $value;
    }
}

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller{

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

    class UserControllerTest extends TestCase 
    {
        private UserController $userController;
        private UserRepository $userRepository;

        protected function setUp(): void {
            $this->userController = new UserController();

            $connection = Database::getConnection();
            $this->userRepository = new UserRepository($connection);
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }

        public function testRegister() {
            $this->userController->register();

            TestCase::expectOutputRegex("[Register]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[Name]");
            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Register New User]");
        }

        public function testPostRegisterSuccess() {
            $_POST['id'] = 'restu';
            $_POST['name'] = 'restu';
            $_POST['password'] = 'restu';

            $this->userController->postRegister();

            TestCase::expectOutputRegex("[Location: /users/login]");
        }

        public function testPostRegisterValidationError() {
            $_POST['id'] = '';
            $_POST['name'] = 'restu';
            $_POST['password'] = 'restu';

            $this->userController->postRegister();

            TestCase::expectOutputRegex("[Register]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[Name]");
            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Register New User]");
            TestCase::expectOutputRegex("[id, name, password cannot blank!]");


        }

        public function testPostRegisterDuplicate() {

            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = "restu";

            $this->userRepository->save($user);

            $_POST['id'] = 'restu';
            $_POST['name'] = 'restu';
            $_POST['password'] = 'restu';

            $this->userController->postRegister();

            TestCase::expectOutputRegex("[Register]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[Name]");
            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Register New User]");
            TestCase::expectOutputRegex("[User is Already Exist!]");
        }
    }
}