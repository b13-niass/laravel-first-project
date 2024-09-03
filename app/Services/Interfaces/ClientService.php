<?php

namespace App\Services\Interfaces;


use App\Http\Requests\AccountForClientRequest;
use App\Http\Requests\ClientByPhoneRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\Request;

interface ClientService
{
    public function all(Request $request);
    public function find($id);
    public function findByPhone(ClientByPhoneRequest $request);
    public function findByDettes($id);
    public function findWithCompte($id);
    public function create($data);
    public function register(array $data, AccountForClientRequest $request);
    public function update(UpdateClientRequest $request, $id);
    public function delete($id);
}
