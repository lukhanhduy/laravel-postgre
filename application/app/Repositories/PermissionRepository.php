<?php

namespace App\Repositories;

use App\Repositories\EloquentRepository;

class PermissionRepository extends EloquentRepository
{
    public function model()
    {
        return \App\Models\Permission::class;
    }
    public function getByRole($roleId){
        return $this->findOneByWhere(["role_id" => $roleId]);
    }
    public function getAll(){
        $this->with('permissions');
        return $this->all([],['*']);
    }
    public function get($id){
        $this->with('permissions');
        return $this->find($id);
    }
}