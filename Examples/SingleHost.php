<?php
/**
 * pfSense WOL 
 * 
 * @author      Nick Phillips (nick@linkstudios.co.uk)
 * @copyright   Copyright 2013, Nick Phillips (nick@linkstudios.co.uk)
 * @license     MIT Licence
 */

require_once __DIR__ . '/../Pfsensewol/Wol.php';

$service = new \Pfsensewol\Wol(array(
    'https' =>      true,
    'pfsense' =>    '10.0.30.1',
    'username' =>   'wol',
    'password' =>   'woluser'
));

$service->send('54:04:a6:b2:61:aa', 'opt4');