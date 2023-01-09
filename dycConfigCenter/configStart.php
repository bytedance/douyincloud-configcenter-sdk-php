#!/usr/bin/env php
<?php
require(dirname(__FILE__) . '/DycConfigClient.php');
use dycConfigCenter\DycConfigClient;

$dycConfigClient = new DycConfigClient();
$dycConfigClient->startLoop();