<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller
{
    require_once __DIR__ . "/../Helper/Helper.php";

    use PHPUnit\Framework\TestCase;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\Session;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
    use ProgrammerZamanNow\Belajar\PHP\MVC\Service\SessionService;

    class UserControllerTest extends TestCase 
    {
        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sesionRepository;

        protected function setUp(): void {
            $this->userController = new UserController();

            $this->sesionRepository = new SessionRepository(Database::getConnection());
            $this->sesionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

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

        public function testLogin() {
            $this->userController->login();

            TestCase::expectOutputRegex("[Login User]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[Password]");
        }

        public function testLoginSuccess() {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = "restu";
            $_POST['password'] = "restu";

            $this->userController->postLogin($user);

            TestCase::expectOutputRegex("[Location: /]");
            TestCase::expectOutputRegex("[X-PZN-SESSION: ]");

        }

        public function testLoginValidationError() {
            $_POST['id'] = "";
            $_POST['password'] = "";

            $this->userController->postLogin();

            TestCase::expectOutputRegex("[Login User]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Id, Password can not blank]");
        }

        public function testLoginUserNotfound() {
            $_POST['id'] = "notfound";
            $_POST['password'] = "notfound";

            $this->userController->postLogin();

            TestCase::expectOutputRegex("[Login User]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Id or password is wrong!]");
        }

        public function testLoginWrongPassword() {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);
            
            $_POST['id'] = "restu";
            $_POST['password'] = "salah";

            $this->userController->postLogin();

            TestCase::expectOutputRegex("[Login User]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Id or password is wrong!]");
        }

        public function logout() {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sesionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            TestCase::expectOutputRegex("[Location: /]");
            TestCase::expectOutputRegex("[X-PZN-SESSION: ]");
        }

        public function testUpdateProfile() {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sesionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            TestCase::expectOutputRegex("[Profile]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[restu]");
            TestCase::expectOutputRegex("[name]");
            TestCase::expectOutputRegex("[restu]");
        }

        public function testPostUpdateProfileSuccess() {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sesionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["name"] = "Budi";
            $this->userController->postUpdateProfile();

            TestCase::expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById("restu");
            TestCase::assertEquals("Budi", $result->name);
        }

        public function testPostUpdateProfileValidationError() {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sesionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST["name"] = "";

            $this->userController->postUpdateProfile();

            TestCase::expectOutputRegex("[Profile]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[restu]");
            TestCase::expectOutputRegex("[Name]");
            TestCase::expectOutputRegex("[Id, Name can not blank]");
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sesionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[restu]");
        }

        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sesionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'restu';
            $_POST['newPassword'] = 'restu12';

            $this->userController->postUpdatePassword();

            TestCase::expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            TestCase::assertTrue(password_verify("restu12", $result->password));
        }

        public function testPostUpdatePasswordValidationError()
        {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sesionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';

            $this->userController->postUpdatePassword();

            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[restu]");
            TestCase::expectOutputRegex("[Id, Old Password, New Password can not blank!]");
        }

        public function testPostUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->id = "restu";
            $user->name = "restu";
            $user->password = password_hash("restu", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = "restu";

            $this->sesionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'salah';
            $_POST['newPassword'] = 'restu123';

            $this->userController->postUpdatePassword();

            TestCase::expectOutputRegex("[Password]");
            TestCase::expectOutputRegex("[Id]");
            TestCase::expectOutputRegex("[restu]");
            TestCase::expectOutputRegex("[Old password is wrong!]");
        }
    }
}