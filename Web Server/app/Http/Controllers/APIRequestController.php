<?php
namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class APIRequestController extends Controller {

    // change country api request handler
    public function change_country(Request $request) {

        // Retrieve country data from config
        $country_data = Config::get('country_data.data');

        // list all country
        $countries = [];

        // get country names
        foreach ($country_data as $country => $country_details) {
            $countries[] = $country;
        }

        if (in_array($request->input('change_country_to'), $countries)) {

            // get country code
            $country_code = $country_data[$request->input('change_country_to')]['country_code'];
            $language_code = $country_data[$request->input('change_country_to')]['language_code'];

            // Set session value
            Session::put('user_country_code', $country_code);
            Session::put('user_language_code', $language_code);

            // String exists in the array
            return response()->json([
                'response' => 'success'
            ]);
        } else {

            // return failed response
            return response()->json([
                'response' => 'failed',
                'message' => 'Country change operation failed! Please refresh and try again.'
            ]);
        }
    }

    // predict disease based on symptoms api request handler
    public function predict_disease(Request $request) {

        // setting validation rules
        $request->validate([
            'symptoms' => 'required|array|min:1',
            'threshold' => 'required|numeric|between:0.00,100.00'
        ]);

        // preparing data for api request
        $data = [
            "threshold" => $request->input("threshold"),
            "symptoms" => $request->input("symptoms")
        ];

        // preparing GuzzleHttp client
        $client = new Client();

        try {

            // sending symptoms to prediction api server for prediction
            $response = $client->post('<API ENDPOINT>', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
            ]);

            // parsing json response
            $json = json_decode($response->getBody()->getContents());

            // getting the user-selected language
            $selectedLanguage = Session::get('user_language_code', 'en');

            // set the application locale to the selected language
            App::setLocale($selectedLanguage);

            // looping through diseases and setting response to localized language
            foreach ($json->disease as $index => $disease) {
                $json->disease[$index]->Description = __($disease->Description);
                $json->disease[$index]->Disease = __($disease->Disease);
                $json->disease[$index]->Specialist = __($disease->Specialist);
            }


            // return result
            return response()->json($json);
        } catch (Exception $e) {

            // return failed response
            return response()->json([
                'response' => 'failed'
            ]);
        }
    }
}
