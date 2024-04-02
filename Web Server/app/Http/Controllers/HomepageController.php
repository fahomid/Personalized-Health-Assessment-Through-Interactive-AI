<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class HomepageController extends Controller {

    /**
     * Show homepage
     */
    public function show_homepage(Request $request): View {

        // getting country data
        $country_data = config('country_data.data');

        // getting the user-selected language
        $selectedLanguage = Session::get('user_language_code', 'en');

        // setting the application locale to the selected language
        App::setLocale($selectedLanguage);

        // getting symptoms in English
        $symptoms_en = config('symptoms');
        $symptoms_local = [];

        // loop through symptoms and build local language
        foreach ($symptoms_en as $value) {
            $symptoms_local[strtolower(str_replace(' ', '_', trim($value)))] = __($value);
        }

        // getting js translations in English
        $js_translations = config('js_translations.en');
        $js_translated = [];

        // looping through and getting translated data
        foreach ($js_translations as $key => $value) {
            $js_translated[$key] = htmlspecialchars(__($value), ENT_QUOTES);
        }

        // formatting js data
        $js_data = [
            'symptoms' => $symptoms_local,
            'translations' => $js_translated
        ];

        // preparing data for view
        $data = [
            'client_ip' => $request->ip(),
            'country_name' => $request->country,
            'country_code' => $request->country_code,
            'language' => $request->language,
            'country_data' => $country_data,
            'js_data' => json_encode($js_data)
        ];
        return view('welcome')->with('data', $data);
    }
}
