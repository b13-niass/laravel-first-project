<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ClientFilter extends ModelFilter
{
    public function setup()
    {
        // Default filter logic, if any
    }

    public function telephone($telephones)
    {
        $telephonesArray = explode(',', $telephones);

        return $this->where(function ($query) use ($telephonesArray) {
            foreach ($telephonesArray as $telephone) {
                $query->orWhere('telephone', 'like', "%$telephone%");
            }
        });
    }

    public function surnom($noms)
    {
        $nomsArray = explode(',', $noms);

        return $this->where(function ($query) use ($nomsArray) {
            foreach ($nomsArray as $nom) {
                $query->orWhere('surnom', 'like', "%$nom%");
            }
        });
    }

    public function adresse($adress)
    {
        $adressArray = explode(',', $adress);

        return $this->where(function ($query) use ($adressArray) {
            foreach ($adressArray as $ad) {
                $query->orWhere('adresse', 'like', "%$ad%");
            }
        });
    }

    public function sort($sortBy = 'created_at,asc')
    {
        // Split the sort parameter into attribute and direction
        list($attribute, $direction) = explode(',', $sortBy);

        // Default direction to 'asc' if not provided or invalid
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        return $this->orderBy($attribute, $direction);
    }
}
