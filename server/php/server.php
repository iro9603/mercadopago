<?php

require __DIR__  . 'vendor/autoload.php';

//REPLACE WITH YOUR ACCESS TOKEN AVAILABLE IN: https://www.mercadopago.com/developers/panel
MercadoPago\SDK::setAccessToken("TEST-11762472748563-081923-f6c1d5d1d637d865580d438da9d492c8-448109497");

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

switch($path){
    case '':
    case '/':
        require __DIR__ . '/../../client/index.html';
        break;
    case '../server/php/server.php':
        $json = file_get_contents("php://input");
        $data = json_decode($json);
        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = (float)$_POST['transactionAmount'];
        $payment->token = $_POST['token'];
        $payment->description = $_POST['description'];
        $payment->installments = (int)$_POST['installments'];
        $payment->payment_method_id = $_POST['paymentMethodId'];
        $payment->issuer_id = (int)$_POST['issuer'];

        $payer = new MercadoPago\Payer();
        $payer->email = $_POST['email'];
        $payer->identification = array( 
            "type" => $_POST['docType'],
            "number" => $_POST['docNumber']
        );
        $payment->payer = $payer;

        $payment->save(); 

        $response = array(
            'status' => $payment->status,
            'message' => $payment->status_detail,
            'id' => $payment->id
        );
        echo json_encode($response);
        break; 
        
    //Serve static resources
    default:
        $file = __DIR__ . '/../../client' . $path;
        $extension = end(explode('.', $path));
        $content = 'text/html';
        switch($extension){
            case 'js': $content = 'application/javascript'; break;
            case 'css': $content = 'text/css'; break;
            case 'png': $content = 'image/png'; break;
        }
        header('Content-Type: '.$content);
        readfile($file);
        break;
}
