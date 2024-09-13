<?php

namespace App\Services\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface GenerateServiceInterface
{
    public function paginate($request);

    public function create($data);

    public function update($data,$language);

}
