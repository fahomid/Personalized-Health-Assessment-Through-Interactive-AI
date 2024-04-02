<!doctype html>
<html lang="en" class="h-100" data-bs-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Fahomid Hassan">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Personal Health Assessment Tool') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- custom css style file -->
    <link href="{{ asset('assets/style/style.css') }}" rel="stylesheet">
</head>
<body class="d-flex h-100 text-center">
<div class="background" >
    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div >
<div class="cover-container d-flex w-100 h-100 mx-auto flex-column">

    <!-- Nav container -->
    <nav class="navbar bg-body-tertiary sticky-top border-bottom pt-0">
        @if(session('user_language_code', 'en') != 'en')
        <div class="alert alert-warning alert-dismissible fade show w-100 mb-0" role="alert">
            <strong>Caution!</strong> On this site, I am using Google Translator to translate content into different languages, which may not produce 100% accuracy and may miss cultural references. Please pardon any issues this may cause. You can switch to English by <span id="switch_to_english" class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">clicking here</span>!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="container-fluid pt-2">
            <a class="navbar-brand me-0 fw-bold" href="/">{{ __('Personal Health Assessment') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <i class="fa fa-cog" aria-hidden="true"></i>
            </button>

            <!-- Bootstrap off canvas nav menu  -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" data-bs-backdrop="static">
                <div class="offcanvas-header">
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body text-start">
                    <ul class="list-group">
                        <li class="list-group-item">{{ __('IP address:') }} {{ $data['client_ip'] }}</li>
                        <li class="list-group-item">{{ __('Current Location:') }} {{ $data['country_name'] }}</li>
                        <li class="list-group-item ">{{ __('Select Your Country') }}</li>
                        <li class="list-group-item">
                            <select class="form-control" id="countrySelect">
                                <option value="">{{ __('Select Your Country') }}</option>
                                @foreach ($data['country_data'] as $country => $country_data)
                                <option value="{{ $country_data['country_code'] }}" data-language="{{ $country_data['language_code'] }}"{{ session('user_country_code') == $country_data['country_code'] ? ' selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                        </li>
                        <li class="list-group-item" id="probability-threshold-setting" data-threshold="0.20">
                            <div class="input-group mt-2">
                                <span class="input-group-text">{{ __('Probability Threshold:') }}</span>
                                <input type="text" class="form-control" id="probability-threshold" value="0.20" disabled>
                                <button type="button" id="edit-probability-threshold" class="btn btn-success border"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                <button type="button" id="set-probability-threshold" class="btn btn-success border"><i class="fa fa-check" aria-hidden="true"></i></button>
                            </div>
                            <p class="form-text ms-2">{{ __('Probability threshold between 0.01 to 1.0!') }}</p>
                            <p id="threshold-change-info" class="form-text ms-2">{{ __('Do not forget to click the checkmark to apply the change!') }}</p>
                        </li>
                        <li class="list-group-item list-group-item-secondary">
                            <div class="form-check form-switch">
                                <label class="form-check-label" for="show-disease-details">{{ __('Show Disease Details') }}</label>
                                <input class="form-check-input" type="checkbox" role="switch" id="show-disease-details">
                            </div>
                        </li>
                        <li id="disease-showing-warning" class="list-group-item">
                            <div class="alert alert-danger d-flex align-items-center mt-2" role="alert">
                                <div>
                                    <strong>{{ __('Warning!') }}</strong> {{ __('This tool is now showing predicted diseases, which may not be 100% accurate. Please do not use this for medical treatment. This should be used only for research purposes.') }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main container -->
    <main class="main-container px-3 m-auto container">

        <!-- Wait container to show ajax wait animation -->
        <div class="bg_wait" style="display: none;">
            <div class="position-absolute top-50 start-50 translate-middle">
                <div class="d-flex justify-content-center">
                    <div class="d-flex align-items-center">
                        <strong role="status">{{ __('Loading...') }}</strong>
                        <div class="spinner-border ms-3" aria-hidden="true"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Landing content,  will be shown by default -->
        <div id="landing-content">
            <h1>{{ __('Health Assessment Tool: Simplifying Your Path to Better Health') }}</h1>
            <p class="lead">{{ __('Our Health Assessment Tool is designed to streamline the process of understanding your health needs. By inputting your symptoms, the tool provides personalized recommendations on the type of healthcare professional you should consider visiting. Whether it is a general practitioner, specialist, or urgent care, our tool offers tailored guidance to help you make informed decisions about your health. Take the first step towards improved well-being with our user-friendly and intuitive Health Assessment Tool.') }}</p>
            <p class="lead">
                <button type="button" class="start-assessment btn btn-success mt-3">{{ __('Start Assessment') }}</button>
            </p>
        </div>

        <!-- Privacy notice container -->
        <div id="assessment-content" style="display: none;">
            <div id="assessment-notice" class="row justify-content-center">
                <div class="bd-callout bd-callout-info bg-transparent">
                    <strong>{{ __('Important Notice') }}</strong><br>
                    <p class="text-danger">{{ __('Thank you for your interest. This is a research tool and should not be used in medical treatment or detecting disease.') }}</p>
                    <p><strong>{{ __('Information Collection:') }}</strong> {{ __('When you use our health assessment tool, we may collect certain personal information from you. This may include your location, preferred language, symptoms, and other health-related information.') }}</p>
                    <p><strong>{{ __('Purpose of Information Collection:') }}</strong> {{ __('The information collected is used solely for the purpose of evaluating your health status and providing appropriate recommendations. It assists us in assessing your health condition and offering personalized guidance. Location and language data aid us in communicating with you in your native language.') }}</p>
                    <p><strong>{{ __('Data Storage and Retention:') }}</strong> {{ __('We do not store any of the information you provide in our database. All data collected during the health assessment process is temporary and will be discarded within 2 hours of completion. We do not retain any personally identifiable information beyond this timeframe.') }}</p>
                    <hr>
                    <p class="mb-0">{{ __('By continuing you consent to the collection and temporary processing of your personal information as described in this notice.') }}</p>
                </div>
                <div class="row py-3 mt-3">
                    <div class="col text-center">
                        <button id="continue-assessment" type="button" class="btn btn-success start-assessment">{{ __('Continue') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1 container, shows common question -->
        <div id="assessment-step-1" style="display: none;">
            <div class="bd-callout bd-callout-info bg-transparent">
                <p><strong>{{ __('Heads up!') }}</strong> {{ __('Are you feeling under the weather? Not sure which doctor to consult? No worries, we are here to help!') }}</p>
                <p>{{ __('Just answer a few simple questions, and we will use our advanced algorithm to match you with the perfect doctor based on your symptoms.') }}</p>
                <p>{{ __('Your health and well-being are our top priorities. Let us get started on the path to feeling better!') }}</p>
            </div>
            <div class="row py-3">
                <div class="col text-start"><button id="feeling-well" type="button" class="btn btn-danger">{{ __('I am feeling well!') }}</button></div>
                <div class="col text-end"><button id="not-feeling-well" type="button" class="btn btn-primary">{{ __('Not feeling Well? Continue!') }}</button></div>
            </div>
        </div>

        <!-- will become visible if user selects "I am feeling well!" -->
        <div id="assessment-feeling-well" style="display: none;">
            <div class="bd-callout bd-callout-info bg-transparent">
                <p>{{ __('Great to hear that you are feeling well!') }}</p>
                <p>{{ __('Remember, maintaining good health is important.') }}</p>
                <p>{{ __('Stay hydrated, get regular exercise, and take care of yourself!') }}</p>
            </div>
            <div class="row py-3">
                <div class="col text-center"><button type="button" class="btn btn-primary start-assessment">{{ __('Start Over Again!') }}</div>
            </div>
        </div>

        <!-- Symptom collection container -->
        <div id="assessment-not-feeling-well" style="display: none;">
            <div class="bd-callout bd-callout-info bg-transparent">
                <p>{{ __('I am sorry to hear that you are not feeling well.') }}</p>
                <p>{{ __('Do not worry, I can recommend a doctor who specializes in treating symptoms like yours.') }}</p>
                <p>{{ __('You are in good hands, and I will help you get the care you need.') }}</p>
            </div>
            <div class="row">
                <div class="col">
                    <div id="step-1-error" class="alert alert-danger alert-dismissible fade show mt-3" role="alert" style="display: none;">
                        <strong>{{ __('Error!') }}</strong> <span id="step-1-error-message"></span>
                    </div>
                </div>
            </div>
            <div class="w-100 position-relative mt-3">
                <input type="text" id="symptoms-input" class="form-control" placeholder="{{ __('Start typing...') }}" autocomplete="off">
                <div id="suggestions" class="list-group"></div>
                <p class="form-text text-start">{{ __('Start typing your symptoms and select from suggestions...') }}</p>
            </div>
            <div class="selected-symptom-container w-100 position-relative mt-3">
                <p class="text-start">{{ __('Selected symptoms:') }}</p>
                <ul id="selected-symptoms" class="list-group list-inline"></ul>
                <p class="no-symptom-note text-danger">{{ __('Nothing selected yet!') }}</p>
            </div>
            <div class="row py-3">
                <div class="col text-start"><button type="button" class="btn btn-danger start-assessment">{{ __('Start Over Again!') }}</button></div>
                <div class="col text-end"><button id="step-1-continue" type="button" class="btn btn-primary">{{ __('Continue') }}</button></div>
            </div>
        </div>

        <!-- Assessment result container -->
        <div id="assessment-result" style="display: none;">
            <div class="bd-callout bd-callout-info bg-transparent">
                <p class="fw-bold">{{ __('Here is the assessment result based on your symptoms') }}</p>
                <p class="mb-0">{{ __('Suggested medical personal:') }} <strong class="preferred-specialist">{{ __('Internal Medicine') }}</strong></p>
                <p class="predicted-disease-info mb-0 mt-3">{{ __('Predicted disease with the highest probability:') }} <strong class="predicted-disease"></strong> (<span class="predicted-probability"></span>)</p>
            </div>
            <div id="predicted-breakdown-container" class="row py-3">
                <p class="fw-bold mt-3">{{ __('Here is a breakdown of the prediction:') }}</p>
                <table class="table table-striped border table-responsive">
                    <thead>
                    <tr>
                        <th scope="col" class="w-25">{{ __('Disease') }}</th>
                        <th scope="col" class="w-25">{{ __('Probability') }}</th>
                        <th scope="col" class="w-25">{{ __('Specialist') }}</th>
                        <th scope="col" class="w-25">
                            <span class="btn-info-instruction" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="{{ __('Click the Expand icon to see disease description') }}">
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </span>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="symptom-prediction-table"></tbody>
                </table>
            </div>
            <div class="row py-3">
                <p class="fw-bold">{{ __('Here is your symptom breakdown:') }}</p>
                <table class="table table-striped border table-responsive">
                    <thead>
                    <tr>
                        <th scope="col">{{ ucfirst(__('symptom')) }}</th>
                        <th scope="col">{{ __('Severity') }}</th>
                    </tr>
                    </thead>
                    <tbody id="symptom-severity-table">
                    </tbody>
                </table>
                <p id="result-suggestion" class="text-primary-emphasis fw-bold mt-3"></p>
                <p id="result-match-note" class="text-success" style="display: none;">{{ __('You should consider visiting a doctor who is ::specialist_doctor:: specialist, as your symptoms align with that specialty.') }}</p>
                <p id="result-no-match-note" class="text-success" style="display: none;">{{ __('You should consider visiting a doctor who is Internal Medicine specialist, as your symptoms does not align with one particular disease.') }}</p>
                <p id="special-result-note"  class="alert alert-warning" role="alert" style="display:none;">{{ __('Please note that this prediction of a specialist doctor based on only one symptom in this result may not be entirely accurate. For a more reliable assessment, it is recommended to consider multiple symptoms. If you are unsure about your symptoms, consulting an Internal Medicine Specialist is advised.') }}</p>
            </div>
            <div class="row py-3">
                <div class="col text-center"><button type="button" class="btn btn-danger start-assessment">{{ __('Start Over Again!') }}</button></div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="sticky-bottom text-black-50 bg-body-tertiary border-top">
        <p class="m-0 p-2">&copy; {{ __('2024') }}</p>
    </footer>

    <!-- Bootstrap toast container -->
    <div class="toast" id="toast-container" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
        <div class="toast-body" id="toast-message"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script> let jsonString = '{!! $data['js_data'] !!}'; </script>
<script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
