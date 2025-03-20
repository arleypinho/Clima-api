<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    // Rota 1 para obter o clima do dia atual
    public function getDiaAtual()
    {

        $cidade = 'Manaus';
        $url = "https://api.open-meteo.com/v1/forecast?latitude=-3.1019&longitude=-60.0250&current_weather=true&timezone=America/Manaus";

        // Criando um cliente HTTP e fazendo a requisição à API
        $cliente = new Client(['verify' => false]);
        $resposta = $cliente->get($url);
        $dadosClima = json_decode($resposta->getBody(), true);

        // Verifica se os dados do clima atual existem na resposta
        if (!isset($dadosClima['current_weather'])) {
            return response()->json(['erro' => 'Dados do clima atual não encontrados.'], 500);
        }

        // Extração dos dados do clima
        $climaAtual = $dadosClima['current_weather'];
        $temperatura = $climaAtual['temperature'];
        $descricaoCodigo = $climaAtual['weathercode'];
        $descricaoClima = $this->interpretarClimaPrevisao($descricaoCodigo);

        // Montando a frase do clima atual
        $frase = "Hoje o clima está {$descricaoClima} em {$cidade}, com temperatura de {$temperatura}°C. Aproveite o dia!";

        return response()->json(['frase' => $frase]);
    }

    // Função para interpretar o código de clima e gerar descrição
    private function interpretarClimaPrevisao($codigo)
    {
        // Mapa de códigos para descrição do clima
        $mapaClimaPrevisao = [
            0 => 'Céu limpo',
            1 => 'Principalmente limpo',
            2 => 'Parcialmente nublado',
            3 => 'Nublado',
            45 => 'Nevoeiro',
            48 => 'Nevoeiro com geada',
            51 => 'Chuva leve',
            53 => 'Chuva moderada',
            55 => 'Chuva densa',
            61 => 'Chuva leve',
            63 => 'Chuva moderada',
            65 => 'Chuva intensa',
            80 => 'Pancadas de chuva leves',
            81 => 'Pancadas de chuva moderadas',
            82 => 'Pancadas de chuva intensas',
            95 => 'Tempestade',
            96 => 'Tempestade com granizo leve',
            99 => 'Tempestade com granizo forte',
        ];

        return $mapaClimaPrevisao[$codigo] ?? 'Clima desconhecido';
    }

    // Rota 2 para obter o clima detalhado de uma cidade específica
    public function getClimaDetalhado(Request $request)
    {

        $cidade = $request->get('city', 'Manaus');
        $latitude = $request->get('lat', -3.1019);
        $longitude = $request->get('lon', -60.0250);

        // URL da API com os parâmetros necessários
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current_weather=true&timezone=America/Manaus";

        $cliente = new Client(['verify' => false]);

        // Faz a requisição à API
        $resposta = $cliente->get($url);
        $dadosClima = json_decode($resposta->getBody(), true);

        // Verifica se os dados do clima atual existem
        if (!isset($dadosClima['current_weather'])) {
            return response()->json(['erro' => 'Dados de clima não encontrados.'], 500);
        }

        // Extração dos dados de clima atual
        $climaAtual = $dadosClima['current_weather'];
        $temperatura = $climaAtual['temperature'] . '°C';
        $velocidadeVento = $climaAtual['windspeed'] . ' km/h';
        $descricaoCodigo = $climaAtual['weathercode'];
        $descricaoClima = $this->interpretarClimaPrevisao($descricaoCodigo);
        $icone = $this->getIconeClima($descricaoCodigo);

        // Retorna os dados detalhados do clima
        return response()->json([
            'cidade' => $cidade,
            'temperatura' => $temperatura,
            'velocidadeVento' => $velocidadeVento,
            'descricao' => $descricaoClima,
            'icone' => $icone,
        ]);
    }

    // Função para mapear os códigos do clima para ícones
    private function getIconeClima($codigo)
    {
        $icones = [
            0 => '☀️', // Céu limpo
            1 => '🌤', // Principalmente limpo
            2 => '⛅', // Parcialmente nublado
            3 => '☁️', // Nublado
            45 => '🌫️', // Nevoeiro
            48 => '🌁', // Nevoeiro com geada
            51 => '🌦️', // Chuva leve
            53 => '🌦️', // Chuva moderada
            55 => '🌧️', // Chuva densa
            61 => '🌧️', // Chuva leve
            63 => '🌧️', // Chuva moderada
            65 => '🌧️', // Chuva intensa
            80 => '🌦️', // Pancadas de chuva leves
            81 => '🌦️', // Pancadas de chuva moderadas
            82 => '🌧️', // Pancadas de chuva intensas
            95 => '⛈️', // Tempestade
            96 => '⛈️', // Tempestade com granizo leve
            99 => '⛈️', // Tempestade com granizo forte
        ];

        return $icones[$codigo] ?? '❓';
    }

    // Rota 3 para obter a previsão do clima para os próximos 7 dias
    public function getPrevisao7Dias(Request $request)
    {
        // Obtendo parâmetros da requisição com valores padrão
        $cidade = $request->get('city', 'Manaus');
        $latitude = $request->get('lat', -3.1019);
        $longitude = $request->get('lon', -60.0250);

        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=temperature_2m_max,temperature_2m_min,precipitation_sum,weathercode&timezone=America/Manaus";

        $cliente = new Client(['verify' => false]);

        // Faz a requisição à API
        $resposta = $cliente->get($url);
        $dadosClima = json_decode($resposta->getBody(), true);

        // Verifica se a previsão diária existe na resposta
        if (!isset($dadosClima['daily'])) {
            return response()->json(['erro' => 'Dados de previsão não encontrados.'], 500);
        }

        // Processa os dados de previsão para os próximos 7 dias
        $previsao = $dadosClima['daily'];
        $diasDaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
        $previsaoFormatada = [];
        $hoje = date('w');

        foreach ($previsao['temperature_2m_max'] as $index => $temperaturaMaxima) {
            $temperaturaMax = $temperaturaMaxima . '°C';
            $temperaturaMin = $previsao['temperature_2m_min'][$index] . '°C';
            $descricaoCodigo = $previsao['weathercode'][$index];
            $descricaoClima = $this->interpretarClimaPrevisao($descricaoCodigo);

            $dia = $diasDaSemana[($hoje + $index) % 7];
            $previsaoFormatada[] = [
                'dia' => $dia,
                'temperatura' => "{$temperaturaMax} / {$temperaturaMin}",
                'descricao' => $descricaoClima
            ];
        }

        return response()->json([
            'cidade' => $cidade,
            'previsao' => $previsaoFormatada
        ]);
    }

    // Rota 4 para obter a temperatura média de ontem
    public function getTemperaturaMediaOntem(Request $request)
    {
        // Obtendo parâmetros da requisição com valores padrão
        $cidade = $request->get('city', 'Manaus');
        $latitude = $request->get('lat', -3.1019);
        $longitude = $request->get('lon', -60.0250);

        // Calculando a data de ontem no formato ISO 8601
        $ontem = date('Y-m-d', strtotime('-1 day'));

        // URL da API com os parâmetros necessários para dados históricos
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=temperature_2m_max,temperature_2m_min&start_date={$ontem}&end_date={$ontem}&timezone=America/Manaus";

        // Criando um cliente HTTP e fazendo a requisição à API
        $cliente = new Client(['verify' => false]);
        $resposta = $cliente->get($url);
        $dadosClima = json_decode($resposta->getBody(), true);

        // Verifica se os dados de clima diário existem
        if (!isset($dadosClima['daily'])) {
            return response()->json(['erro' => 'Dados de clima de ontem não encontrados.'], 500);
        }

        // Processa os dados de temperatura de ontem
        $temperaturaMaxima = $dadosClima['daily']['temperature_2m_max'][0];
        $temperaturaMinima = $dadosClima['daily']['temperature_2m_min'][0];

        // Calcula a temperatura média
        $temperaturaMedia = round(($temperaturaMaxima + $temperaturaMinima) / 2, 1);

        // Formata a resposta
        return response()->json([
            'cidade' => $cidade,
            'temperatura_media' => "{$temperaturaMedia}°C"
        ]);
    }

    // Rota 5 para converter a temperatura
    public function converterTemperatura(Request $request)
    {
        // Obtendo os parâmetros da requisição
        $temperatura = $request->get('temperatura');
        $unidadeDestino = strtoupper($request->get('unidade')); // Garantir que a unidade seja em maiúsculo (C, F, K)

        // Verifica se a temperatura foi fornecida
        if (!$temperatura) {
            return response()->json(['erro' => 'Temperatura não fornecida.'], 400);
        }

        // Verifica se a unidade de destino é válida
        if (!in_array($unidadeDestino, ['C', 'F', 'K'])) {
            return response()->json(['erro' => 'Unidade de destino inválida. Use C, F ou K.'], 400);
        }

        // Funções de conversão
        $temperaturaConvertida = null;
        switch ($unidadeDestino) {
            case 'C':
                // Converter para Celsius
                $temperaturaConvertida = $this->converterParaCelsius($temperatura);
                break;
            case 'F':
                // Converter para Fahrenheit
                $temperaturaConvertida = $this->converterParaFahrenheit($temperatura);
                break;
            case 'K':
                // Converter para Kelvin
                $temperaturaConvertida = $this->converterParaKelvin($temperatura);
                break;
        }

        // Retorna a temperatura original e a convertida
        return response()->json([
            'temperatura_original' => "{$temperatura}°C", // Considerando que a entrada está em Celsius
            'convertida' => "{$temperaturaConvertida}°{$unidadeDestino}"
        ]);
    }

    // Função para converter Celsius para Fahrenheit
    private function converterParaFahrenheit($temperatura)
    {
        return round(($temperatura * 9 / 5) + 32, 1);
    }

    // Função para converter Celsius para Kelvin
    private function converterParaKelvin($temperatura)
    {
        return round($temperatura + 273.15, 1);
    }

    // Função para converter Fahrenheit para Celsius
    private function converterParaCelsius($temperatura)
    {
        return round(($temperatura - 32) * 5 / 9, 1);
    }

    // Função para converter Kelvin para Celsius
    private function converterParaKelvinDeCelsius($temperatura)
    {
        return round($temperatura - 273.15, 1);
    }


    // Rota 6 para obter o horário do nascer e pôr do sol
    public function getNascerPorDoSol(Request $request)
    {
        // Obtendo parâmetros da requisição com valores padrão
        $cidade = $request->get('city', 'Manaus');
        $latitude = $request->get('lat', -3.1019);
        $longitude = $request->get('lon', -60.0250);

        // URL da API para obter dados do nascer e pôr do sol
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=sunrise,sunset&timezone=America/Manaus";

        // Criando um cliente HTTP e fazendo a requisição à API
        $cliente = new Client(['verify' => false]);
        $resposta = $cliente->get($url);
        $dadosSol = json_decode($resposta->getBody(), true);

        // Verifica se os dados foram retornados corretamente
        if (!isset($dadosSol['daily'])) {
            return response()->json(['erro' => 'Dados de nascer e pôr do sol não encontrados.'], 500);
        }

        // Obtendo os horários de nascer e pôr do sol e convertendo para o fuso horário local
        $nascerDoSolUTC = new \DateTime($dadosSol['daily']['sunrise'][0]);
        $porDoSolUTC = new \DateTime($dadosSol['daily']['sunset'][0]);
        $timezone = new \DateTimeZone('America/Manaus');
        $nascerDoSolUTC->setTimezone($timezone);
        $porDoSolUTC->setTimezone($timezone);

        // Formata os horários para o formato desejado (h:i A - 12h formato)
        $nascerDoSolFormatado = $nascerDoSolUTC->format('h:i A');
        $porDoSolFormatado = $porDoSolUTC->format('h:i A');

        // Formata a resposta
        return response()->json([
            'cidade' => $cidade,
            'nascer_do_sol' => $nascerDoSolFormatado,
            'por_do_sol' => $porDoSolFormatado
        ]);
    }


    // Rota 7 para obter a previsão de chuva
    public function getPrevisaoDeChuva(Request $request)
    {
        // Obtendo parâmetros da requisição com valores padrão
        $cidade = $request->get('city', 'Manaus'); // Usar o valor passado para "city", se disponível
        $latitude = $request->get('lat', -3.1019);  // Latitude (se não passado, usa valor padrão)
        $longitude = $request->get('lon', -60.0250);  // Longitude (se não passado, usa valor padrão)

        // URL da API para obter dados da previsão do tempo
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=precipitation_sum&timezone=America/Manaus";

        // Criando um cliente HTTP e fazendo a requisição à API
        $cliente = new Client(['verify' => false]);
        $resposta = $cliente->get($url);
        $dadosChuva = json_decode($resposta->getBody(), true);

        // Verifica se os dados foram retornados corretamente
        if (!isset($dadosChuva['daily']['precipitation_sum'])) {
            return response()->json(['erro' => 'Dados de previsão de chuva não encontrados.'], 500);
        }

        // Obtendo a previsão de chuva para os próximos dias
        $precipitacao = $dadosChuva['daily']['precipitation_sum'];

        // Verifica se há previsão de chuva para os próximos dias
        $previsao = "Sem previsão de chuva";
        if ($precipitacao[0] > 0 || $precipitacao[1] > 0 || $precipitacao[2] > 0) {
            $previsao = "Chuva prevista para os próximos 3 dias";
        }

        // Formata a resposta
        return response()->json([
            'cidade' => $cidade,
            'previsao' => $previsao
        ]);
    }

    // Rota 8 para comparar a temperatura de hoje com a de ontem
    public function compararTemperatura(Request $request)
    {
        // Obtendo parâmetros da requisição com valores padrão
        $cidade = $request->get('city', 'Manaus');
        $latitude = $request->get('lat', -3.1019);  // Latitude (se não passado, usa valor padrão)
        $longitude = $request->get('lon', -60.0250);  // Longitude (se não passado, usa valor padrão)

        // URL da API para obter dados de temperatura de hoje e ontem
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=temperature_2m_max,temperature_2m_min&timezone=America/Manaus";

        // Criando um cliente HTTP e fazendo a requisição à API
        $cliente = new Client(['verify' => false]);
        $resposta = $cliente->get($url);
        $dadosTemperatura = json_decode($resposta->getBody(), true);

        // Verifica se os dados foram retornados corretamente
        if (!isset($dadosTemperatura['daily']['temperature_2m_max']) || !isset($dadosTemperatura['daily']['temperature_2m_min'])) {
            return response()->json(['erro' => 'Dados de temperatura não encontrados.'], 500);
        }

        // Obtendo as temperaturas de hoje e ontem
        $temperaturaHoje = $dadosTemperatura['daily']['temperature_2m_max'][0];  // Máxima de hoje
        $temperaturaOntem = $dadosTemperatura['daily']['temperature_2m_max'][1];  // Máxima de ontem

        // Comparando as temperaturas
        $comparacao = "Hoje está mais quente que ontem.";
        if ($temperaturaHoje < $temperaturaOntem) {
            $comparacao = "Hoje está mais frio que ontem.";
        } elseif ($temperaturaHoje == $temperaturaOntem) {
            $comparacao = "Hoje está com a mesma temperatura de ontem.";
        }

        // Formata a resposta
        return response()->json([
            'cidade' => $cidade,
            'ontem' => "{$temperaturaOntem}°C",
            'hoje' => "{$temperaturaHoje}°C",
            'comparacao' => $comparacao
        ]);
    }
}
