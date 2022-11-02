<?php

namespace SRC\Controller;

use SRC\Repository\UserRepository;
use SRC\Configuration\Config;

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
            return [
                'Status' => 'Error',
                'Message' => "This Login is taken, chose another login",
            ];
        }
        
        $data['role'] = (isset($data['role'])) ?  1 : 0;

        $token = $this->generateToken($this->generateString());
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
        
        $token = $this->generateToken($this->generateString());
        $insertedToken = $this->userRep->insertTokenToUser($user['id'], $token);
        
        if($insertedToken == false){
            return [
                'Status' => 'Error',
                'Message' => "Can't registrate you in my sistem",
            ];
        }

        $user['token'] = $token;
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

    public function generateToken($word, $n = 7)
    {
   
        $lowerLetters = 'abcdefghijklmnopqrstuvwxyz';
        $arrLetters = str_split($lowerLetters);
        $resArr = [];
        for($i = 0; $i < strlen($word); $i++){
            $val = array_search($word[$i],$arrLetters) + 1;
            for($j = 0; $j < $n; $j++){
                $val *= 3;
                $val -= 5;
            }
            $resArr[] = $val;
        }
        return $resArr;
    }
    public function decrypt($word, $n)
    {
        $lowerLetters = 'abcdefghijklmnopqrstuvwxyz';
        $arrLetters = str_split($lowerLetters);
        $resArr = [];
        for($i = 0; $i < count($word); $i++){
            $num = $word[$i];
            for($j = 0; $j < $n; $j++){
                $num += 5;
                $num /= 3;
            }
            $resArr[] =$arrLetters[$num -1];
        }
        return implode('',$resArr);
    }

    public function generateString($n = 10){
        $characters = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $randomString = '';
 
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return substr_replace($randomString, Config::$code, rand(1, $n), 0);
    }

}