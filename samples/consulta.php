#!/usr/bin/env php -q
<?php
$load = require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$load->add('MrPrompt', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src');

use MrPrompt\Portabilidade\Telefone;
use MrPrompt\Portabilidade\Captcha;
use MrPrompt\Portabilidade\Consulta;

try {
	echo "Informe o telefone que deseja consultar: ";

	$telefone = new Telefone;
	$telefone->setNumero(chop(fgets(STDIN)));

	echo "Baixando captcha...", PHP_EOL;

	$portabilidade = new Consulta;
	$captcha       = $portabilidade->baixaCaptcha();

	if ($captcha instanceof Captcha) {
		echo "Captcha baixado, tentando abrir...", PHP_EOL;

		passthru("open {$captcha->getImagem()}");

		echo "Entre com o texto da imagem: ";

		$captcha->setTexto(chop(fgets(STDIN)));
	}

	$retorno = $portabilidade->consulta($telefone, $captcha);

	printf("Operadora: %s\n", $retorno);
} catch (\Exception $e) {
	printf('Erro inesperado: %s - #%i\n', $e->getMessage(), $e->getCode());
}
