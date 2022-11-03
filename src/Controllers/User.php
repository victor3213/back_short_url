<?php

namespace SRC\Controller;

use SRC\Repository\UserRepository;
use SRC\Configuration\Config;
use SRC\Controller\TokenController;

class User 
{
    private $userRep;
    
    private $token;

    public function __construct()
    {
        $this->userRep = new UserRepository();
        $this->token = new TokenController();
    }

    public function registerUser($data)
    {
        if($this->userRep->checkLogin($data['login'])){
            return [
                'Status' => 'Error',
                'Message' => "This Login is taken, chose another login",
            ];
        }
        
        $data['role'] = (isset($data['role'])) ?  1 : 0;

        $token =  $this->token->generateToken( $this->token->generateString());
        $user = $this->userRep->insertUser($data, $token); 

        if($user == false){
            return [
                'Status' => 'Error',
                'Message' => "Can't registrate you in my sistem",
            ];
        }

        $data['token'] = $token;
        return [
            'Status' => 'Success',
            'Message' => 'Usere Loged',
            'data' => $data
        ];
    }

    public function loginUser($data)
    {
        if(!isset($data['login']) && !isset($data['password'])){
            return [
                'Status' => 'Error',
                'Message' => "The login or password is missing",
            ];
        }

        $user = $this->userRep->loginUser($data);

        if($user == false){
            return [
                'Status' => 'Error',
                'Message' => "Can't registrate you in my sistem",
            ];
        }
        
        $token =  $this->token->generateToken( $this->token->generateString());
        $insertedToken = $this->userRep->insertTokenToUser($user['id'], $token);
        
        if($insertedToken == false){
            return [
                'Status' => 'Error',
                'Message' => "Can't registrate you in my sistem",
            ];
        }

        $user['token'] = $token;
        unset($user['id']);
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