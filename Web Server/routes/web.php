<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\APIRequestController;

// route for country change api request
Route::post('/api/v1/country-change', [APIRequestController::class, 'change_country']);

// route for prediction api
Route::post('/api/v1/predict', [APIRequestController::class, 'predict_disease']);

// route for homepage
Route::get('/', [HomepageController::class, 'show_homepage']);

// my experimental route for exporting sting for translation
/*Route::get('/test', function (Request $request) {

    // Read the contents of the JSON file
    $jsonContents = file_get_contents('bn.json');

    // Decode the JSON contents into an associative array
    $jsonArray = json_decode($jsonContents, true);
    $json_keys = array_keys($jsonArray);
    print_r($json_keys);

    // Get all array keys
    //$arrayKeys = array_keys($jsonArray);

    // Convert the keys array into a JavaScript array format
    //$javascriptArray = json_encode($arrayKeys, JSON_HEX_APOS);
    //$javascriptArray = config('symptoms.en');


    // Specify the file path where you want to save the JSON file
    $filePath = 'output.json';

    // Save the JSON string to the file
    file_put_contents($filePath, json_encode($json_keys));

    echo "JSON array saved to $filePath";


});*/
