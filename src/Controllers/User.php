<?php

namespace SRC\Controller;

use SRC\Repository\UserRepository;

class User 
{
    private $userRep;

    public function __construct()
    {
        $this->userRep = new UserRepository();
    }

    public function registerUser($data)
    {
        if($this->userRep->checkLogin($data['login'])){
            return ['Error' => 'This Login is taken'];
        }
        
    }

    public function loginUser()
    {

    }
}