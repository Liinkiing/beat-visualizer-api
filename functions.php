<?php

namespace Utils {

    function onOffToBoolean(string $onOff): bool
    {
        return $onOff === 'on';
    }

    function msToMinutesSeconds(int $ms): string
    {
        $input = $ms;

        $uSec = $input % 1000;
        $input = floor($input / 1000);

        $seconds = $input % 60;
        $seconds = str_pad($seconds, 2, 0, STR_PAD_LEFT);
        $input = floor($input / 60);

        $minutes = $input % 60;

        return "$minutes:$seconds";
    }

}
