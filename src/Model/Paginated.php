<?php


namespace App\Model;


interface Paginated
{
    public static function createPaginatedFromApi(array $apiResponse, string $itemClassname);
}
