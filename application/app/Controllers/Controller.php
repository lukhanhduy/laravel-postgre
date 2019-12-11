<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use JWTAuth;
class Controller extends BaseController
{   protected $repository;
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __construct(){
    }
    /**
     * this function to parse token to user
     */
    public function user($token){
        $user = JWTAuth::toUser($token);
        return $user;
    }
    public function getUserId($token){
        $user = JWTAuth::toUser($token);
        return $user->userId;
    }
}
