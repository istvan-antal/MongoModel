<?php
require_once '../lib/MongoModel.php';
require_once './lib/Item.php';

$item = new Item();
$item->setTitle('Title');
$item->setDescription('Lorem ipsum sit amet dolor.');
$item->store();

