<?php

namespace App\Repositories;

use App\Repositories\EloquentRepository;

class RoleRepository extends EloquentRepository
{
    public function model()
    {
        return \App\Models\Role::class;
    }
    public function getAll(){
        $this->with('permissions');
        return $this->all([],['*']);
    }
    public function get($id,$include = "permission"){
        if(!empty($include)){
            $this->with('permissions');
        }
        return $this->find($id);
    }
}