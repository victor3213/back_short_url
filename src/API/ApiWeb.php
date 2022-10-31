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
        $this->action = isset($dataPost['action']) ? $dataPost['action'] : null;
        
        switch ($this->action) {
            case "getShortUrl":
                $this->execute(new ShortUrlController(), $dataPost);
                break;
            case "loginUser":
            case "registerUser":
                $this->execute(new User(), $dataPost);
                break;
            default:
                $this->result = ['Error' => 'This action ' . $this->action . ' is not valid'];
                // logg in bd
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