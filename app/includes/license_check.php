<?php

require_once __DIR__.'/../config/database.php';

$stmt = $pdo->query("
SELECT license_key
FROM license_settings
LIMIT 1
");

$license = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$license){
    header("Location: activate_license.php");
    exit;
}

$license_key = $license['license_key'];

$domain = '2dmegarwn.eu';

$ch = curl_init('https://levantimind.eu/license-api/check.php');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'license_key' => $license_key,
    'domain'      => $domain,
    'product'     => 'schoolmedia'
]);

$response = curl_exec($ch);

if(curl_errno($ch)){

    die('Σφάλμα επικοινωνίας: '.curl_error($ch));

}

$data = json_decode($response, true);

if(!$data){

    die('Μη έγκυρη απάντηση από License Server.');

}

if($data['status'] != 'active'){
    header("Location: activate_license.php");
    exit;
}