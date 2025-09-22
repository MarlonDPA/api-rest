Baixar projeto
O env.backup vai estar com as configuracões de uso, vai estar presente no pacote.
Estou usando o Xampp apache com mysql
Criar o banco cplug pelo comando no mysql
create database cplug

Rodar a migrate no comando
php artisan migrate
php artisan migrate:fresh --seed

Fila
conferir no env 
QUEUE_CONNECTION=database
rodar para ligar a fila
php artisan queue:work

Endpoint
GET - http://localhost:8000/api/inventory
GET - http://localhost:8000/api/reports/sales
GET - http://localhost:8000/api/reports/sales/1 
GET - http://localhost:8000/api/reports/sales?status=completed&date_start=2025-09-01&date_end=2025-09-20

Para post no inventory, gerando pelo produto com quantidade, pode ser pelo id ou pelo sku
endpoit post - http://localhost:8000/api/inventory
{
  "product_id": 1,
  "quantity": 5
}
-------------------------------------
{
  "sku": "P001",
  "quantity": 10
}

Para post no sales, para inserir um ou mais produtos na venda, pode usar dois status, completed ou pending, o pending náo dá baixa no estoque
endpoit POST - http://localhost:8000/api/sales
{
  "status": "completed",
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 2,
      "quantity": 3
    }
  ]
}

Testes, rodar o comando
php artisan test --testsuite=Unit

Para limpar resgitros com mais de 90 dias, foi implementado no laravel na pasta de commands o arquivo 
CleanOldInventory,  mas recomendaria efetuar a limpeza direto dentro no banco usando o cron job interno.
