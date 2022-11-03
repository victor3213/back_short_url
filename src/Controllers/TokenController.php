<?php

namespace SRC\Controller;

use SRC\Configuration\Config;

class TokenController 
{
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

    public function decryptToken($word, $n = 7)
    {
        $word = json_decode($word, true);
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

    public function generateString($n = 10)
    {
        $characters = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $randomString = '';
 
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return substr_replace($randomString, Config::$code, rand(1, $n), 0);
    }
    
    public function checkToken($token)
    {
        $string = $this->decryptToken($token);
        
        if(strpos($string, Config::$code)){
            return true;
        }
        return false;
    }
}