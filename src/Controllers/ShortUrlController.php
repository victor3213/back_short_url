<?php

namespace SRC\Controller;

use SRC\Repository\Url;

class ShortUrlController
{
    private $url;

    public function __construct()
    {
        $this->url = new Url();
    }

    public function getShortUrl($data)
    {
        if(!$this->checkUrlIsValid($data['longUrl'])){
            return ['Error' => 'The Url is not valid'];
        }

        $nameShortUrl = '';
        if(isset($data['typeOfUrl'])){
            if($data['typeOfUrl'] == 'simple'){
                $nameShortUrl = $this->generateRandomName();
            } else {
                $nameShortUrl = $data['nameUrl'];
            }
        }
        $userId = isset($data['userId']) ? $data['userId'] : 0;
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
            return ['Warning' => 'This url exist '. $existentUrl['shortUrl'] ];
        }
        
        $newUrl = $this->url->insertNewUrl($data);
        if($newUrl !== false){
            return ['Success' => 'Your link now is ' . $newUrl];
        } 
        return ['Error' => 'Fatal Error' ];
    }
}