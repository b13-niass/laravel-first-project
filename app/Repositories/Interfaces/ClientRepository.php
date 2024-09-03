<?php

namespace App\Repositories\Interfaces;


interface ClientRepository
{
    public function all($request);
    public function find($id);
    public function findByPhone($phone);
    public function findByDettes($id);
    public function findWithCompte($id);
    public function create($data);
    public function register($userData, $allData);
    public function update($request,$id);
    public function delete($id);

}
