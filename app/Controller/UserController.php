<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\UserService;

class UserController 
{
    private UserService $userService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
    }

    public function register()  {
        View::render("User/register", [
            'title' => 'Register New User',
        ]);
    }

    public function postRegister() {
        $user = new UserRegisterRequest();
        $user->id = $_POST['id'];
        $user->name = $_POST['name'];
        $user->password = $_POST['password'];

        try{
            $this->userService->register($user);
            View::redirect('/users/login');
            
        }catch(ValidationException $exception) {
            View::render("User/register", [
                'title' => 'Register New User',
                'error' => $exception->getMessage()
            ]);
        }
    }
}