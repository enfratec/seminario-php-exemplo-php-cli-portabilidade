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

/**
 * @uses \MrPrompt\Portabilidade\Telefone
 */
use MrPrompt\Portabilidade\Telefone;

/**
 * @uses \MrPrompt\Portabilidade\Captcha
 */
use MrPrompt\Portabilidade\Captcha;

class Consulta
{
    /**
     * @var string
     */
    private $cookie = '/tmp/cookies_portabilidade.txt';

    /**
     * @var string
     */
    private $captcha = '/tmp/captcha_portabilidade.jpg';

    /**
     * @var string
     */
    private $url = 'http://consultanumero.abr.net.br:8080/consultanumero';

    /**
     * @var object
     */
    private $curl;

    /**
     * @var string
     */
    private $jcid;

    /**
	 * Construtor
	 *
	 * Inicio o client cURL
     */
    public function __construct()
    {
		// iniciando cURL
		$agent  = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-BR; rv:1.9.1.5)'; 
		$agent .= ' Gecko/20091102 Firefox/3.5.5';

		$this->curl              = new \Curl;
		$this->curl->cookie_file = $this->cookie;
		$this->curl->user_agent  = $agent;
    }

    /**
     * Busca o captcha e tenta quebrar
     *
	 * @return boolean
	 * @throws \Exception
     */
    public function baixaCaptcha ()
    {
        // envio a primeira chamada
		$url     = $this->url . '/consulta/consultaSituacaoAtual';
		$retorno = $this->curl->get($url);

		preg_match_all('/jcid=([[:alnum:]])+/i', $retorno->body, $jcids);

        $this->jcid = str_replace('jcid=', '', $jcids[0][0]);

        // Pegando o captcha, que tem sempre o mesmo nome
        $ret = $this->curl->get($this->url . '/jcaptcha.jpg?jcid=' . $this->jcid);

		file_put_contents($this->captcha, $ret->body);

        if (filesize($this->captcha) === 0) {
            throw new \Exception('Erro baixando captcha.');
        }

		$captcha = new Captcha;
		$captcha->setImagem($this->captcha);

        return $captcha;
    }

    /**
     * Segunda etapa, envio dos dados.
	 *
	 * @param \MrPrompt\Portabilidade\Telefone $telefone
	 * @param \MrPrompt\Portabilidade\Captcha $captcha
     * @return mixed
     */
    public function consulta(Telefone $telefone, Captcha $captcha)
    {
        $campos = array(
            'nmTelefone'         => $telefone->getNumero(),
            'j_captcha_response' => $this->captcha,
            'jcid'               => $this->jcid,
            'method:consultar'   => 'Consultar'
        );

        // enviando o captcha
        $url = $this->_url . '/consulta/consultaSituacaoAtual.action';
        $retorno = $this->curl->post($url, $campos);

        return $this->trataRetorno($retorno);
    }

    /**
     * Trata o retorno da consulta em busca da string válida
     *
     * @param string $retorno
     * @return string
     */
    private function trataRetorno($retorno)
    {
        // removendo quebras de linha
        $retorno = preg_replace('/(\r|\n)/', '', $retorno);

        // Buscando pela operadora
        $er = '/(<tr class="gridselecionado">(.+)<\/tr>)/i';
        preg_match_all($er, $retorno, $resposta);

        if (isset($resposta[0][0]) && strlen($resposta[0][0]) > 2) {
            $retorno = explode('<td>', $resposta[0][0]);

            // sucesso, retorno a operadora
            if (isset($retorno[2])) {
                return ucwords(strip_tags(utf8_encode($retorno[2])));
            }
        }
    }
}
