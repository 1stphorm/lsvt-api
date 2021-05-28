<?php

namespace PhormDev\LSVT;

/**
 * Class Facade
 * @method getUserByUsername(string $username);
 * @method addPoints(int $user_id, int $points);
 * @method deductPoints(int $user_id, int $points);
 * @method bulkPoints(array $data);
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return API::class;
    }
}
