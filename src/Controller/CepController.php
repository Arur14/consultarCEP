<?php

namespace App\Controller;

use App\Form\CepType;
use App\Service\CepStorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CepController extends AbstractController
{
    private $httpClient;
    private $cepStorageService;
    private $cache;

    public function __construct(HttpClientInterface $httpClient, CepStorageService $cepStorageService, CacheInterface $cache)
    {
        $this->httpClient = $httpClient;
        $this->cepStorageService = $cepStorageService;
        $this->cache = $cache;
    }

    /**
     * @Route("/consultar-cep", name="consultar_cep")
     */
    public function consultarCep(Request $request): Response
    {
        // cria o formulario para exibir na tela
        $form = $this->createForm(CepType::class);
        $form->handleRequest($request);

        //verifica se foi realizado ação do botão
        if ($form->isSubmitted() && $form->isValid()) {
            $cep = $form->getData()['cep'];

            // validar se o cep para eviar apenas numeros
            $cep = str_replace('-', '', $cep);
            $cep = str_replace('.', '', $cep);
            $cep = str_replace(',', '', $cep);

            //redirecionar para fazer a consulta do cep via API
            return $this->redirectToRoute('resultadoCep', ['cep' => $cep]);
        }

        return $this->render('cep/consultar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/resultado-cep/{cep}", name="resultado_cep")
     */
    public function resultadoCep(string $cep): Response
    {
        $data = null;
        if(strlen($cep) == 8){
            // verifica se os dados estão em cache
            $cacheData = $this->cepStorageService->getCepData($cep);

            //verifica se exites dados
            if ($cacheData) {
                $data = $cacheData;
            } else {
                // faz a requisição para na API
                $response = $this->httpClient->request('GET', "https://viacep.com.br/ws/$cep/json/");
                $data = $response->toArray();

                // verifica se houve erro na resposta da API
                if (isset($data['erro'])) {
                    // tratamento de error, quando o CEP é inválido
                    throw new \Exception("CEP não encontrado."); 
                }

                // grava os dados em cache
                $this->cepStorageService->storeCepData($cep, $data);
            }

            //redireciona para exibir os resultados
            return $this->render('cep/resultado.html.twig', ['data' => $data]);
        }else{
            // redireciona para pagina de erro para informar ao usuario
            return $this->render('cep/error.html.twig', ['data' => $cep]);
        }
    }
}

