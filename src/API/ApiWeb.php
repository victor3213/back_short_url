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
        $this->action = isset($dataGet['action']) ? $dataGet['action'] : null;
        switch ($this->action) {
            case "getShortUrl":
                $this->execute(new ShortUrlController(), $dataGet);
                break;
            case "loginUser":
            case "registerUser":
                $this->execute(new User(), $dataGet);
                break;
            default:
                $this->result = ['Error' => 'This action ' . $this->action . ' is not valid'];
                // logg in bd
                break;
        }
    }

    public function execute($class, $l)
    {
        $action= $this->action;
        if(method_exists($class, $action)){
            $this->result = $class->$action($l);
        }else{
            $this->result = ['Error' => 'This methond ' . $this->action . 'not exist' ];
            // log in bd
        }
    }

    public function response()
    {
        // header("Content-type: application/json; charset=utf-8");
        echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
    }
}