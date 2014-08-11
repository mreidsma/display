<?php

date_default_timezone_set('America/Detroit');

if (isset($_GET['roomId'])) {

    // conference style 305
    $url = 'http://gvsu.edu/reserve/files/cfc/functions.cfc?method=bookings&roomId='.$_GET['roomId'].'&startDate=2014-08-11&endDate=2014-08-11';

    $xml = new SimpleXMLElement(file_get_contents($url));

    /*
    echo '<pre>';
    print_r($xml);
    echo '</pre>';
    */



    foreach ($xml->Data as $reservation) {

        $timeStart = substr($reservation->TimeEventStart, strpos($reservation->TimeEventStart, "T") + 1);
        $timeEnd = substr($reservation->TimeEventEnd, strpos($reservation->TimeEventEnd, "T") + 1);

        $now = new dateTime();
        $now_format = $now->format('H:i:00');
        $now = strtotime($now_format);

        /*
        echo '<br>now formated: ' . (string)$now_format;
        echo '<br>time start: ' . (string)$timeStart;
        echo '<br>time end: ' . (string)$timeEnd;
        */

        //
        if ($now > strtotime($timeStart) && $now < strtotime($timeEnd)) {

            $reservations = array(
                "Room" => (string)$reservation->Room,
                "GroupName" => (string)$reservation->GroupName,
                "TimeStart" => $timeStart,
                "TimeEnd" => $timeEnd,
                "Now" => $now_format,
                "Status" => "reserved"
            );

            echo json_encode($reservations);
            break;

        } elseif (($now + 3600) > strtotime($timeStart) && $now < strtotime($timeEnd)) {
            $reservations = array(
                "Room" => (string)$reservation->Room,
                "GroupName" => (string)$reservation->GroupName,
                "TimeStart" => $timeStart,
                "TimeEnd" => $timeEnd,
                "Now" => $now_format,
                "Status" => "reserved_soon"
            );

            echo json_encode($reservations);
            break;
        }

    }
}