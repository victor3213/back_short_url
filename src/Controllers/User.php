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
        $data['role'] = (isset($data['role'])) ?  1 : 0;
        if($this->userRep->checkLogin($data['login'])){
            return [
                'Status' => 'Error',
                'Message' => "This Login is taken, chose another login",
            ];
        }
        if($this->userRep->insertUser($data)){
            
            return [
                'Status' => 'Success',
                'Message' => 'Usere Loged',
                'data' => $data
            ];
        }
        return [
            'Status' => 'Error',
            'Message' => "Can't registrate you in my sistem",
        ];
    }

    public function loginUser($data)
    {
        $user = $this->userRep->loginUser($data);

        if($user == false){
            return ['Error' => 'Sorry but, you are not ours, the password or login is wrong'];
        }
        return ['Success' => 'Usere Loged'];
    }
}