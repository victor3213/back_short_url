<?php

namespace SRC\Controller;

use SRC\Repository\Url;
use SRC\Configuration\Config;
use SRC\Controller\TokenController;
use SRC\Repository\UserRepository;
class ShortUrlController
{
    private $url;

    private $token;

    private $userRep;

    public function __construct()
    {
        $this->url = new Url();
        $this->token = new TokenController();
        $this->userRep = new UserRepository();
    }

    public function getShortUrl($data)
    {
        if (!isset($data['longUrl']) ) {
            return [
                'Status' => 'Error',
                'Message' => 'Empty Url' 
            ];
        }

        if(!$this->checkUrlIsValid($data['longUrl'])){
            return [
                'Status' => 'Error',
                'Message' => 'The Url is not valid' 
            ];
        }
        $userId = '';
        if(isset($data['token'])){
            $userData = $this->userRep->checkUserWithToken($data['token']);
            if(!$this->token->checkToken($data['token'])  || $userData == false){
                return [
                    'Status' => 'Error',
                    'Message' => 'Something is wrong, try again later'
                ];
            }
            $userId = $userData[0]['id'];
        }
        
        $nameShortUrl = '';
        
        if(!isset($data['typeOfUrl']) || $data['typeOfUrl'] == 'simple'){
            $nameShortUrl = $this->generateRandomName();
        } else {
            $nameShortUrl = $data['nameUrl'];
        }

        $userId = !empty($userId) ? $userId : -1;
        $prepareData = [
            'userId' => $userId,
            'nameShortUrl' => $nameShortUrl,
            'longUrl' => $data['longUrl']
        ];

        $result = $this->checkUrl($prepareData);
        return $result;
    }

    public function checkUrlIsValid($url)
    {
        if(filter_var($url, FILTER_VALIDATE_URL) == false || strlen($url) == 0){
            return false;
        }
        return true;
    }


    public function generateRandomName($maxSize = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomStr = '';
        
        for($i = 0; $i <= $maxSize; $i++){
            $randomStr .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomStr;
    }

    public function checkUrl($data)
    {
        if($existentUrl = $this->url->checkUrl($data)){
            return [
                'Status' => 'Warning',
                'Message' => 'This url exist '. $existentUrl['shortUrl'],
                'data' =>  Config::$hostPort . $existentUrl['shortUrl'] 
            ];
        }
        
        $newUrl = $this->url->insertNewUrl($data);
        if($newUrl !== false){
            return [
                'Status' => 'Success',
                'Message' => 'Your link now is ' . $newUrl,
                'data' => Config::$hostPort . $newUrl
            ];
        }
        return [
            'Status' => 'Error',
            'Message' => 'Fatal Error' 
        ];
    }

    public function getUrls($data)
    {
        if(!isset($data['token']) && !isset($data['all'])){
            return [
                'Status' => 'Error',
                'Message' => 'Something is not right' 
            ];
        }
        $userData = $this->userRep->checkUserWithToken($data['token']);
        if(!$this->token->checkToken($data['token'])  || $userData == false){
            return [
                'Status' => 'Error',
                'Message' => 'Something is wrong, try again later'
            ];
        }
        $data['userId'] = $userData[0]['id'];
        $allUrls = $this->url->getAllUrls($data);

        if($allUrls == false){
            return [
                'Status' => 'Error',
                'Message' => 'There are no data' 
            ];
        }
        return [
            'Status' => 'Success',
            'Message' => 'Or found ' . $allUrls['count'] . ' data',
            'data' => $allUrls
        ];
    }

    public function openOriginalLink()
    {
        $originalLink = $this->url->getOriginalLink();
        if ($originalLink === false) {
            header("Location: https://www.google.com/");
        }else{
            header("Location: ".  $originalLink);
        }
        exit;
    }
}