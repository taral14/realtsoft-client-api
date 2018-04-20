# Realtsoft API - User class
An official PHP class for work with Realtsoft API.

## Requirements:
- PHP >= 5.3.0

## Installation
### Via Сomposer
```sh
composer require "taral14/realtsoft-client-api @dev"
```

###  Code example
```php
<?php
use RealtsoftApi\Client;
$api = new Client('https://your-project.realtsoft.net', KEY, SECRET);
// find contacts
$rows = $api->call('client/index', [
    'phone' => PHONE_NUMBER,
    'email' => CLIENT_EMAIL,
]);
// if contact not found
if(empty($rows)) {
    // create new client
    $response = $api->call('client/create', [
        'phones' => [CLIENT_PHONE_1, CLIENT_PHONE_2],
        'name' => SET_CLIENT_NAME,
        'email' => SET_CLIENT_EMAIL,
        'responsible_user_id' => 1, // responsible user
    ], 'POST');
    if(!$response->success) {
        echo 'Failed to create contact';
        echo print_r($response->errors);
        exit;
    }
    $client = $response->model;
} else {
    $client = $rows[0];
}
// create new inquiry
$response = $api->call('inquiry/create', [
    'responsible_user_id' => 1,
    'deal' => INQUIRY_DEAL,
    'realty_type' => INQUIRY_REALTY_TYPE,
    'category' => INQUIRY_CATEGORY,
    'name' => INQUIRY_NAME,
    'price_kind' => 'per_object',
    'client_id' => $client->id,
    'source_kind' => INQUIRY_SOURCE_KIND,
], 'POST');

if($response->success) {
    echo 'SUCCESS';
    echo print_r($response->model);
} else {
    echo 'ERROR';
    echo print_r($response->errors);
}
```
