<?php


function secondsToTime($inputSeconds) {
    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;

    // Extract days
    $days = floor($inputSeconds / $secondsInADay);

    // Extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // Extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // Extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // Format and return
    $timeParts = [];
    $sections = [
        'gün' => (int)$days,
        'saat' => (int)$hours,
        'dakika' => (int)$minutes,
        'saniye' => (int)$seconds,
    ];

    foreach ($sections as $name => $value){
        if ($value > 0){
            $timeParts[] = $value. ' '.$name;
        }
    }

    return implode(', ', $timeParts);
}

function yetkili_adi($ad, $gid)
{
    switch ($gid) {
        case '2':
            $yetkili_gorsel = '<span class="text-danger">' . $ad . '</span>';
            break;
        case '3':
            $yetkili_gorsel = '<span class="text-primary">' . $ad . '</span>';
            break;
        case '4':
            $yetkili_gorsel = '<span class="text-success">' . $ad . '</span>';
            break;
        case '5':
            $yetkili_gorsel = '<span class="text-info">' . $ad . '</span>';
            break;
        case '6':
            $yetkili_gorsel = '<span class="text-primary">' . $ad . '</span>';
            break;
        
        default:
            $yetkili_gorsel = $ad;
            break;
    }

    return $yetkili_gorsel;
}

function sunucu_adi($ad, $sid)
{
    switch ($sid) {
        case '1':
            $sunucu_gorsel = '<span class="text-danger">' . $ad . '</span>';
            break;
        case '2':
            $sunucu_gorsel = '<span class="text-warning">' . $ad . '</span>';
            break;
        case '3':
            $sunucu_gorsel = '<span class="text-warning">' . $ad . '</span>';
            break;
        case '4':
            $sunucu_gorsel = '<span class="text-success">' . $ad . '</span>';
            break;
        case '5':
            $sunucu_gorsel = '<span class="text-info">' . $ad . '</span>';
            break;
        case '6':
            $sunucu_gorsel = '<span class="text-primary">' . $ad . '</span>';
            break;
        
        default:
            $sunucu_gorsel = $ad;
            break;
    }

    return $sunucu_gorsel;
}

function bayrak($code) {
    $code = '<i class="flag-icon flag-icon-' . strtolower($code) . ' rounded shadow-sm"></i>';
    return $code;
}

function onay($text) {
    return '<div class="alert alert-success" role="alert"><i class="fas fa-check mr-2"></i> ' . $text . '</div>';
}

function hata($text) {
    return '<div class="alert alert-danger" role="alert"><i class="fas fa-times mr-2"></i> ' . $text . '</div>';
}
