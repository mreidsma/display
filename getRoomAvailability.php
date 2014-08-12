<?php

date_default_timezone_set('America/Detroit');

if (isset($_GET['roomId'])) {

    $today = new dateTime();
    $today = $today->format('Y-m-d');

    $url = 'http://gvsu.edu/reserve/files/cfc/functions.cfc?method=bookings&roomId='.$_GET['roomId'].'&startDate='.$today.'&endDate='.$today.'';
    $xml = new SimpleXMLElement(file_get_contents($url));

    foreach ($xml->Data as $reservation) {

        $timeStart = substr($reservation->TimeEventStart, strpos($reservation->TimeEventStart, "T") + 1);
        $timeEnd = substr($reservation->TimeEventEnd, strpos($reservation->TimeEventEnd, "T") + 1);

        $now = new dateTime();
        $now_format = $now->format('H:i:00');
        $now = strtotime($now_format);


        // Ignore GVSU API User
        if ($reservation->GroupName != "GVSU-API User") {

            //
            if ($now > strtotime($timeStart) && $now < strtotime($timeEnd)) {

                $reservations = array(
                    "Room" => (string)$reservation->Room,
                    "GroupName" => (string)$reservation->GroupName,
                    "TimeStart" => $timeStart,
                    "TimeEnd" => $timeEnd,
                    "Now" => $now_format,
                    "EventName" => (string)$reservation->EventName,
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
                    "EventName" => (string)$reservation->EventName,
                    "Status" => "reserved_soon"
                );

                echo json_encode($reservations);
                break;
            }

        }

    }
}