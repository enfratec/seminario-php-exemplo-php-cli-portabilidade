#!/usr/bin/env php -q
<?php
$load = require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$load->add('MrPrompt', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src');

use MrPrompt\Portabilidade\Telefone;
use MrPrompt\Portabilidade\Captcha;
use MrPrompt\Portabilidade\Consulta;

$telefone = new Telefone;
$telefone->setNumero('4891858982');

$portabilidade = new Consulta;
$captcha       = $portabilidade->baixaCaptcha();

echo "Captcha baixado\n";
echo "Entre com o texto da imagem: ";

$captcha->setTexto(chop(fgets(STDIN)));

