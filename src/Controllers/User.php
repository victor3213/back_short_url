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
        $user = $this->userRep->insertUser($data); 
        if($user != false){
            return [
                'Status' => 'Success',
                'Message' => 'Usere Loged',
                'data' => $user
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
            return [
                'Status' => 'Error',
                'Message' => "Can't registrate you in my sistem",
            ];
        }
           
        return [
            'Status' => 'Success',
            'Message' => 'Usere Loged',
            'data' => $user
        ];
    }

    public function getAllUsers($data)
    {
        if(!$this->userRep->checkIfIsAdmin($data['userId'])){
            return [
                'Status' => 'Error',
                'Message' => "You are not an admin and you cannot see the data",
            ];
        }

        $users = $this->userRep->getAllUsers();
        if($users == false){
            return [
                'Status' => 'Error',
                'Message' => "There are no other users",
            ];
        }
        
        $result = [];
        $result['count'] = count($users);
        $result['data'] = $users;
        
        return [
            'Status' => 'Success',
            'Message' => 'Or found users',
            'Data' => $result
        ];
    }

    public function updataDataAboutUser($data)
    {
        if(!$this->userRep->checkIfIsAdmin($data['userId'])){
            return [
                'Status' => 'Error',
                'Message' => "You are not an admin and you cannot update some data",
            ];
        }

        $updatedData = $this->userRep->updateDataAboutUser($data);
        if($updatedData){
            return [
                'Status' => 'Success',
                'Message' => "The data has been updated",
            ];
        }

        return [
            'Status' => 'Error',
            'Message' => "The data has not been updated",
        ];
    }
}