<?php

namespace PhormDev\LSVT\Facades;

use PhormDev\LSVT\API;

/**
 * Class Facade
 * @method static getUserByUsername(string $username);
 * @method static addPoints(int $user_id, int $points);
 * @method static deductPoints(int $user_id, int $points);
 * @method static bulkPoints(array $data);
 */
class LSVT extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return API::class;
    }
}
