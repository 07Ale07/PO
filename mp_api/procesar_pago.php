<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

MercadoPago\SDK::setAccessToken($_ENV['MP_ACCESS_TOKEN']);

$preference = new MercadoPago\Preference();

// Producto de prueba
$item = new MercadoPago\Item();
$item->title = 'Prueba de Pago';
$item->quantity = 1;
$item->unit_price = 100.00;
$preference->items = array($item);

// URLs de retorno
$preference->back_urls = array(
    "success" => "http://localhost/mp-test/index.php?estado=success",
    "failure" => "http://localhost/mp-test/index.php?estado=failure",
    "pending" => "http://localhost/mp-test/index.php?estado=pending"
);
$preference->auto_return = "approved";

$preference->save();

echo json_encode([
    'id' => $preference->id
]);
