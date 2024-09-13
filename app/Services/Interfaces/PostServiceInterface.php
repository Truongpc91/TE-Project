<?php

namespace App\Services\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface PostServiceInterface
{
    public function paginate($request, $languageId);

    public function create($data, $language);

    public function update($data,$post, $language);
}
