<?php

namespace App\Repositories;

use App\Repositories\EloquentRepository;

class ModuleRepository extends EloquentRepository
{
    public function model()
    {
        return \App\Models\Module::class;
    }
    public function getAll(){
        return $this->allWithoutPagination();
    }
}