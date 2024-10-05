<?php

namespace App\Services\Interfaces;

/**
 * Interface AttributeServiceInterface
 * @package App\Services\Interfaces
 */
interface SourceServiceInterface
{
    public function paginate($request, $languageId);
}
