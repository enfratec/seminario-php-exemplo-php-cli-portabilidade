<?php
/**
 * Telefone
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

class Telefone
{
	/**
	 * @var string
	 */
	private $numero;

	/**
	 * To string method
	 */

	/**
	 * Set telefone
	 *
	 * @return \MrPrompt\Portabilidade\Telefone
	 */
    public function setNumero($telefone)
    {
        $this->numero = $this->limpaTelefone($telefone);

        return $this;
    }

	/**
	 * Get telefone
	 *
	 * @return string
	 */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Limpa o telefone dado como entrada
     *
     * @param string $telefone
     * @return integer
     */
    private function limpaTelefone($telefone)
    {
        // verifico se existe um telefone de destino
        if ($telefone === null || strlen($telefone) !== 10) {
            throw new Exception('Telefone inválido.');
        }

        // limpo o telefone
        if ($telefone !== null) {
            $telefone = preg_replace('/[^[:digit:]]/', '', $telefone);

            return $telefone;
        }
    }
}
