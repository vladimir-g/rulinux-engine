<?php
$ok = rand(0, 1);
$date_dog = rand(126043320, time()-36000);;

switch($ok){
    case 1:
        $otg = $date_dog;
    break;
    case 0:
        $otg = $date_dog + rand(100000, 200000);
    break;
}
echo $ok.' => '.date('Y-m-d', $otg).':'.date('Y-m-d', $date_dog);
?>