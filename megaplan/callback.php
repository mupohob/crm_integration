<?php
/**
 * @VolkovAlexander
 */

require './../vendor/autoload.php';
require './../lib/MegaplanToZadarma.php';

$logClient = new \lib\MegaplanToZadarma();

if(!empty($_POST)) {
    $logClient->Log->notice('New request', $_POST);
} else {
    $logClient->Log->error('New request with empty data');
}