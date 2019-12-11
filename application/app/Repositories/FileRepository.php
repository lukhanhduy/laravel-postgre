<?php

namespace App\Repositories;

use App\Repositories\EloquentRepository;

class FileRepository extends EloquentRepository
{
    public function model()
    {
        return \App\Models\Files::class;
    }
    public function save($files, $options = []){
        $isLocale =  empty($options['isLocale']) ? true : false;
        $objectId = empty($options['object_id']) ? 1 : $options['object_id'];
        $objectType = empty($options['object_type']) ? 1 : $options['object_type'];
        $created_by = empty($options['created_by']) ? null : $options['created_by'];
        $insertData = [];
        if(!is_array($files)){
            $file = $files;
            $result = $this->doUpload($file);
            $createdData = $this->create([
                'file_path' => $result['url'],
                'is_locale' => $isLocale,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'file_name' => $file->getClientOriginalName(),
                'object_id' => $objectId,
                'object_type' => $objectType,
                'created_by' => $created_by
            ]);
            return $createdData;
        }
        foreach ($files as $file) {
            $this->doUpload($file);   
            $insertData[] = [
                'file_path' => $result['url'],
                'is_locale' => $isLocale,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'file_name' => $file->getClientOriginalName()
            ];
        }
        $createdData = $this->create($insertData);
        return $createdData;
    }
    public function doUpload($file){
        $fileType = explode( '/', $file->getMimeType())[0];
        if($fileType == 'image'){
            \Cloudder::upload($file);
        }
        else if($fileType == 'video'){
            \Cloudder::uploadVideo($file);
        }
        $result =\Cloudder::getResult(); 
        return $result;
    }
}