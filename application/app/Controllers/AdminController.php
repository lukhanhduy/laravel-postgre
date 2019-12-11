<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminUpdateRequest;

use App\Http\Controllers\Controller;
use Helper;
use HandleHttp;
use HandleSql;
use JWTAuth;
use Hash;
use App\Repositories\AdminRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\FileRepository;

class AdminController extends Controller
{
    public function __construct(
        AdminRepository $repository, 
        RoleRepository $roleRepository,
        ModuleRepository $moduleRepository,
        FileRepository $fileRepository
    )
    {
      
        \Config::set('jwt.user',  \App\Models\Admin::class);
        \Config::set('auth.providers', ['users' => [
                'driver' => 'eloquent',
                'model' =>  \App\Models\Admin::class,
        ]]);
        $this->roleRepository = $roleRepository;
        $this->repository = $repository;
        $this->moduleRepository =  $moduleRepository;
        $this->fileRepository = $fileRepository;
        parent::__construct();
    }
    /**
     * this function to login to admin and get token
     */
    public function fnLogin(AdminLoginRequest $request){
        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
             return HandleHttp::responseError([
                 'code' => 422,
                 'message' => __('message.wrongPassword')
             ]);
            }
         } catch (JWTAuthException $e) {
            return HandleHttp::responseError([
                'code' => 500,
                'message' => __('message.invalidToken')
            ]);
         }
         $user = $this->user($token);
         $role = $this->roleRepository->get($user->role_id);
         $permissions = $role->permissions;
         $moduleIds = [];
         foreach ($permissions as  $permission) {
            $rules = json_decode($permission->rules);
            if(count($rules) > 0 ){
                $moduleIds[] =  $permission->module_id;
            }
         }
         $modules = $this->moduleRepository->findWhereInWithOutPagination("module_id",$moduleIds);
         unset($role->permissions);
         return HandleHttp::responseSuccess([
             "data" => [
                 "user" => $user,
                 "accessToken"=> $token,
                 "role" => $role,
                 "permissions" => $permissions,
                 "modules" => $modules
             ]
         ]);
    }
    /**
     * this function create admin from admin page
     */
    public function fnCreate(AdminCreateRequest $request){
        $email = Helper::get($request,'email','');
        $password = Helper::get($request,'password','');
        $phoneNumber = Helper::get($request,'phone_number','');
        $firstName = Helper::get($request,"first_name",'');
        $lastName = Helper::get($request,'last_name','');
        $result = $this->repository->create([
            "email" => $email,
            "password" => $password,
            "phone_number" => $phoneNumber,
            "first_name" => $firstName,
            "last_name" => $lastName
        ]);
        $result["message"] = __('message.created',["module"=>__("global.account")]);
        return HandleHttp::responseSuccess($result);
    }
    
    public function fnUpdate(AdminUpdateRequest $request){
        $params = $request->only('email', 'password', 'phone_number', 'first_name','last_name', 'user_id');    
        $created_by = $this->getUserId($request->token);
        $userId = $request->user_id;
        if ($request->hasFile('file')) {   
            $files = $request->file('file'); 
            $result = $this->fileRepository->save($files,['created_by' => $created_by, 'object_type' => 2, 'object_id' => $userId ]);
        }
        $password = Helper::get($request,'password','');
        $phoneNumber = Helper::get($request,'phone_number','');
        $firstName = Helper::get($request,"first_name",'');
        $lastName = Helper::get($request,'last_name','');
        $insertData = [];
        foreach ($params as $key => $value) {
            $insertData[$key] = $value;
        }
        if(count($insertData) > 0 ){
            $result = $this->repository->update($insertData,$userId);
            return $result;
        }
        return $insertData;
    }

    public function fnGetAll(Request $request){
        $admins = $this->repository->with("role")->all();
        return $admins;
    }
}
