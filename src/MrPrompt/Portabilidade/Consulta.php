<?php
/**
 * Consulta a operadora de celular
 *
 * Licença
 *
 * Este código fonte está sob a licença GPL-3.0+, você pode ler mais
 * sobre os termos na URL: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author  Thiago Paes <thiago@thiagopaes.com.br> (c) 2013
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 * @package
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
    private $cookie = 'portabilidade.txt';

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
		$this->curl->cookie_file = sys_get_temp_dir() 
								 . DIRECTORY_SEPARATOR 
								 . $this->cookie;
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
		// desabilito a checagem de erros, pq o html é externo e já sabe...
		libxml_use_internal_errors(true);

        // envio a primeira chamada
		$url     = $this->url . '/consulta/consultaSituacaoAtual';
		$retorno = $this->curl->get($url);

		// carrego o documento
		$doc = new \DOMDocument;
		$doc->loadHTML($retorno->body);

		// procuro o iframe com o captcha
		$xpath  = new \DOMXPath($doc);
		$iframe = $xpath->query("//iframe")->item(0)->getAttribute('src');

		$retorno = $this->curl->get($iframe);
		$doc2    = new \DOMDocument;
		$doc2->loadHTML($retorno->body);

		// procuro o captcha
		$xpath      = new \DOMXPath($doc2);
		$imgCaptcha = $xpath->query("//img")->item(0)->getAttribute('src');
		$urlCaptcha = 'http://www.google.com/recaptcha/api/' . $imgCaptcha;

		$captcha = new Captcha;
		$captcha->setImagem($urlCaptcha);

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
            'j_captcha_response' => $captcha->getTexto(),
            'jcid'               => $this->jcid,
            'method:consultar'   => 'Consultar'
        );

        $url     = $this->url . '/consulta/consultaSituacaoAtual';
        $retorno = $this->curl->post($url, $campos);

		$doc = new \DOMDocument;
		$doc->loadHTML($retorno->body);

		$path  = new \DOMXPath($doc);
		$grids = $path->query('//body')->item(0)->childNodes;

		// se não for encontrado conteúdo no tbody, é pq não achou o telefone.
		if ($grids->length == 0) {
			throw new \Exception('Telefone não identificado.', 500);
		}

		return $grids->item(1)->nodeValue;
    }
}
