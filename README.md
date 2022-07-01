# Payments API

Este projeto é um exemplo de implementação de uma API para gerenciar contas financeiras e executar transações entre estas contas, semelhante à um sistema de pagamentos ou um sistema bancário.

## Sobre

### Requisitos:

 - PHP
 - MySQL
 - Redis
 - Composer

### Premissas:

-   Para ambos tipos de usuário, precisamos do Nome Completo, CPF, e-mail e Senha. CPF/CNPJ e e-mails devem ser únicos no sistema. Sendo assim, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou endereço de e-mail.
    
-   Usuários podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários.
    
-   Lojistas  **só recebem**  transferências, não enviam dinheiro para ninguém.
    
-   Validar se o usuário tem saldo antes da transferência.
    
-   Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo, use este mock para simular ([https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6](https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6)).
    
-   A operação de transferência deve ser uma transação (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia.
    
-   No recebimento de pagamento, o usuário ou lojista precisa receber notificação (envio de email, sms) enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este mock para simular o envio ([http://o4d9z.mocklab.io/notify](http://o4d9z.mocklab.io/notify)).
    
-   Este serviço deve ser RESTFul.

## Instalação

1. Primeiro clone o projeto:
	```bash
	git clone https://github.com/giovanne-oliveira/payments-api.git
	```
2. Instale as dependências utilizando o Composer
	```bash
	composer install
	```
3. Copie o arquivo de ambiente exemplo 
	```bash
	cp .env.example .env
	```
4. Preencha os arquivo de ambiente com os dados do banco de dados e do Redis
	```
	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=test
	DB_USERNAME=root
	DB_PASSWORD=password
	QUEUE_CONNECTION=redis
	REDIS_HOST=127.0.0.1
	REDIS_PASSWORD=null
	REDIS_PORT=6379
	```
5. Gere a chave de criptografia do Laravel
	```bash
	php artisan key:generate
	```
6. Importe as tabelas para o banco de dados
	```bash
	php artisan migrate
	```
7. Configure o ambiente de filas utilizando o Horizon e o Supervisor, conforme demonstrado na [documentação oficial](https://laravel.com/docs/9.x/horizon#installing-supervisor)

## Endpoints

#### Headers
Esta aplicação aceitará payloads do tipo `application/json`

### Criar uma transação

 - Método: POST
 - Endpoint: /api/transaction/create
 - Body:
	```json
	{
		"payee": {{PAYEE_USER_ID}},
		"payer": {{PAYER_USER_ID}},
		"amount": {{TRANSACTION_AMOUNT}}
	}
	``` 
- Variáveis:
	PAYEE_USER_ID: ID do usuário que receberá a transferência. Usuário recebedor.
	PAYER_USER_ID: ID do usuário que emitirá a transferência. Usuário pagador.
	TRANSACTION_AMOUNT: Valor da transferência, expressado em formato double.

- Retorno esperado (HTTP 201):
	```json
	{
		"data": {
			"id": "b38e7671-a3ff-4ec2-a535-6e60c06fa3b3",
			"amount": 10,
			"created_at": "2022-07-01T19:21:13.000000Z",
			"payer": {
				"name": "Giovanne Oliveira",
				"email": "tyra.feest@example.org",
				"is_store": 0
			},
			"payee": {
				"name": "Super Cool Store",
				"email": "dgoldner@example.com",
				"is_store": 1
			}
		}
	}
	```
### Listar transações

 - Método: GET
 - Endpoint: /api/transactions
 - Body: nenhum
 - Retorno espero (HTTP 200):
	```json
	{
		"data": {
			"transactions": [
				{
					"id": "b38e7671-a3ff-4ec2-a535-6e60c06fa3b3",
					"amount": "10.00",
					"created_at": "2022-07-01T19:21:13.000000Z",
					"payer": {
						"name": "Giovanne Oliveira",
						"email": "tyra.feest@example.org",
						"is_store": 0
					},
					"payee": {
						"name": "Super Cool Store",
						"email": "dgoldner@example.com",
						"is_store": 1
					}
				}
			]
		}
	}
	```
### Consultar saldo de uma conta
 - Método: GET
 - Endpoint: /api/account/info/{{ACCOUNT_ID}}
 - Body: Nenhum
- Variáveis: 
	**ACCOUNT_ID:** ID da conta a ser consultada.
- Retorno esperado (HTTP 200):
	```json
	{
		"data": {
		"accountId": "ca2a7756-bdcd-4633-8d53-878a85298ce7",
		"accountOwner": {
			"name": "Giovanne Oliveira",
			"email": "tyra.feest@example.org",
			"is_store": 0
		},
		"isActive": 1,
		"balance": "90.00"
		}
	}
	```
### Reverter uma transação
- Método: DELETE
 - Endpoint: /api/transaction/{{TRANSACTION_ID}}
 - Body: Nenhum
 - Variáveis:
	 **TRANSACTION_ID:** ID da transação. Pode ser obtido no retorno da chamada de criação da transação, na propriedade **id**
- Retorno esperado (HTTP 200):
	```json
	{
		"success": true,
		"message": "Transaction deleted successfully"
	}
	```

## Testes
Este projeto possui testes unitários, validando os os pontos de premissa do projeto. A testagem pode ser feita através do framework Pest, executando o comando abaixo:
```bash
./vendor/bin/pest
```
