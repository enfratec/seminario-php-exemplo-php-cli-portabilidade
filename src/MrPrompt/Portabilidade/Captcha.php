<?php
/**
 * Cliente_Portabilidade
 *
 * Consulta a operadora de celular
 *
 * Licença
 *
 * Este código fonte está sob a licença Creative Commons, você pode ler mais
 * sobre os termos na URL: http://creativecommons.org/licenses/by-sa/2.5/br/
 *
 * @copyright  Thiago Paes <thiago@thiagopaes.com.br> (c) 2010
 * @license    http://creativecommons.org/licenses/by-sa/2.5/br/
 */
namespace MrPrompt\Portabilidade;

class Captcha
{
	/**
	 * @var string
	 */
	private $imagem;

	/**
	 * @var string
	 */
	private $texto;

	/**
	 * Set imagem
	 *
	 * @param string $imagem
	 * @return \MrPrompt\Portabilidade\Captcha
	 */
	public function setImagem($imagem)
	{
		$this->imagem = $imagem;

		return $this;
	}

	/**
	 * Get imagem
	 *
	 * @return string
	 */
	public function getImagem()
	{
		return $this->imagem;
	}

	/**
	 * Set texto
	 *
	 * @param string $texto
	 * @return \MrPrompt\Portabilidade\Captcha
	 */
	public function setTexto($texto)
	{
		$this->texto = filter_var($texto, \FILTER_SANITIZE_STRING);

		return $this;
	}

	/**
	 * Get texto
	 *
	 * @return string
	 */
	public function getTexto()
	{
		return $this->texto;
	}
}
