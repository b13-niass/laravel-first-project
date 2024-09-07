<?php

namespace App\Repositories\Interfaces;

interface DetteRepository
{
    public function all();
    public function find($id);
    public function findByClient($id);
    public function create($data);
    public function payer($data);
}
