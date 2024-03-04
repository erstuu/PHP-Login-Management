<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use Exception;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Exception\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterResponse;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginResponse;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    // Register Service Request
    public function register(UserRegisterRequest $request): UserRegisterResponse 
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();
            
            $user = $this->userRepository->findById($request->id);
            if($user != null) {
                throw new ValidationException("User is Already Exist!"); 
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;
            
            Database::commitTransaction();
            
            return $response;
        } catch(Exception $exception) {
            Database::rollbackTransaction();
            
            throw $exception;
        }
    }

    // Validate Register Service Request
    private function validateUserRegistrationRequest(UserRegisterRequest $request) {
        if($request->id == null || $request->name == null || $request->password == null ||
        trim($request->id) == "" || trim($request->name) == null || trim($request->password) == "") 
        {
            throw new ValidationException("id, name, password cannot blank!");
        }
    }

    // Login Service Request
    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if ($user == null) {
            throw new ValidationException("Id or password is wrong!");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;

            return $response;
        } else {
            throw new ValidationException("Id or password is wrong!");
        }
    }

    // Validate Login Request
    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null || $request->password == null ||
            trim($request->id) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Password can not blank");
        }
    }
}