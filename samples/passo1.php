<?php
$load = require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$load->add('MrPrompt', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src');

$portabilidade = new Portabilidade();
$portabilidade->setTelefone('4891858982');
$portabilidade->consulta();

