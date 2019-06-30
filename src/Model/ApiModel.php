<?php


namespace App\Model;


interface ApiModel
{
    public static function createFromApi(array $apiResponse);
}
