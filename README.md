# API de Clima com Laravel/Lumen

Este projeto é uma API de clima desenvolvida com **Laravel/Lumen**, que consome dados da API externa **OpenWeather** para fornecer informações sobre o clima, como temperatura, previsão do tempo, e outros dados relacionados.

## Requisitos

Antes de rodar o projeto, você precisa garantir que os seguintes requisitos estão atendidos:

- **PHP** (versão 8 ou superior): (https://www.php.net/downloads)
- **Composer** (para gerenciar dependências do PHP): (https://getcomposer.org/download/)

### Passos para rodar o projeto

### 1. Instalar o PHP
- Caso você ainda não tenha o PHP instalado, acesse o [site oficial](https://www.php.net/downloads) e faça o download a partir da versão 8 ou superior.
  
### 2. Instalar o Composer
- Baixe e instale o Composer a partir do [site oficial](https://getcomposer.org/download/).

### 3. Clonar o repositório do projeto
No terminal, clone o repositório do projeto ou baixe o arquivo .Zip se preferir

### 4. Instalar as dependências do projeto
Na raiz do seu projeto utilize o comando: 
 
composer install

### 5. Inicializar o servidor local
Para rodar o servidor local, utilize o comando abaixo:

php -S localhost:8000 -t public

### 6. Testar as rotas da API

Com o servidor rodando, você pode acessar as seguintes URLs diretamente no seu navegador ou através de ferramentas como **Insominia** ou **Postman** para testar a API:

- **Dia Atual**:  
  [http://localhost:8000/api/dia-atual](http://localhost:8000/api/dia-atual)

- **Clima Detalhado**:  
  [http://localhost:8000/api/clima-detalhado](http://localhost:8000/api/clima-detalhado)

- **Previsão de 7 dias**:  
  [http://localhost:8000/api/previsao-7-dias](http://localhost:8000/api/previsao-7-dias)

- **Temperatura de Ontem**:  
  [http://localhost:8000/api/temperatura-ontem](http://localhost:8000/api/temperatura-ontem)

- **Conversor de Temperatura**:  
  [http://localhost:8000/api/converter-temperatura?temperatura=30&unidade=C](http://localhost:8000/api/converter-temperatura?temperatura=30&unidade=C)

- **Nascer e Pôr do Sol**:  
  [http://localhost:8000/api/nascer-por-do-sol?city=Manaus&lat=-3.1019&lon=-60.0250](http://localhost:8000/api/nascer-por-do-sol?city=Manaus&lat=-3.1019&lon=-60.0250)

- **Previsão de Chuva**:  
  [http://localhost:8000/api/previsao-de-chuva?lat=-3.1019&lon=-60.0250](http://localhost:8000/api/previsao-de-chuva?lat=-3.1019&lon=-60.0250)

- **Comparar Temperatura**:  
  [http://localhost:8000/api/comparar-temperatura?city=S%C3%A3o%20Paulo&lat=-23.5505&lon=-46.6333](http://localhost:8000/api/comparar-temperatura?city=S%C3%A3o%20Paulo&lat=-23.5505&lon=-46.6333)

### 7. Verifique Controladores e Rotas

- **Controladores**:  
  Para visualizar a lógica de cada rota, acesse a pasta `app/Http/Controllers`. Os controladores contêm os métodos que processam as requisições e interagem com a API externa do OpenWeather.

- **Rotas**:  
  As rotas da API estão definidas em `routes/api.php`. É nesse arquivo que você pode configurar novas rotas ou ajustar as existentes.


