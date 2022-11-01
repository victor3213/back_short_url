<?php

namespace SRC\API;

use SRC\Controller\ShortUrlController;
use SRC\Controller\User;

class ApiWeb 
{
    private $result;
    private $action;

    function __construct($dataGet, $dataPost)
    {
        $dataPost = json_decode($dataPost, true);
        $data = (empty($dataGet)) ? $dataPost : $dataGet;
        $this->action = isset($data['action']) ? $data['action'] : null;
        
        switch ($this->action) {
            case "getShortUrl":
            case "getUrls":
                $this->execute(new ShortUrlController(), $data);
                break;
            case "loginUser":
            case "registerUser":
            case "getAllUsers":
                $this->execute(new User(), $data);
                break;
            default:
                $this->action = 'openOriginalLink';
                $this->execute(new ShortUrlController(), $data);
                break;
        }
    }

    public function execute($class, $l)
    {
        $action = $this->action;
        if (method_exists($class, $action)) {
            $this->result = $class->$action($l);
        } else {
            $this->result = ['Error' => 'This methond ' . $this->action . 'not exist' ];
        }
    }

    public function response()
    {
        // header("Content-type: application/json");
        echo json_encode($this->result);
    }
}