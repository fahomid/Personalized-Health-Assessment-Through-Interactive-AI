$(document).ready(function(){

    // enable tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // remove selected item on click
    $("#selected-symptoms").on("click", ".close", function() {
        $(this).closest("li").remove();
    });

    // autocomplete symptoms list
    let js_data = JSON.parse(jsonString);
    let symptoms_list = js_data.symptoms;
    let js_translations = js_data.translations;

    // search symptom and suggest
    $('#symptoms-input').on('input', function() {

        // getting input data and element
        let input = $(this).val().trim().toLowerCase().replace(/ /g, '_');
        let suggestionsDiv = $('#suggestions');

        // removing old elements
        suggestionsDiv.empty();

        // only show suggestion if at least one character was type
        if (input.length < 1) {
            return;
        }

        // get already selected symptoms
        const selected_symptoms = {};
        $('#selected-symptoms li').each((_, elem) =>
            selected_symptoms[$(elem).attr('data-symptom').trim().toLowerCase().replace(/ /g, '_')] = $(elem).text().trim()
        );

        // get matching symptoms
        const matchingSymptoms = Object.entries(Object.fromEntries(Object.entries(symptoms_list).filter(([key, value]) => key.includes(input) || value.includes(input))));

        // filter out already selected symptoms
        const filteredMatchingSymptoms = matchingSymptoms.filter(([key]) => !selected_symptoms.hasOwnProperty(key));

        // only keep first 5
        const slicedData = Object.fromEntries(Object.entries(filteredMatchingSymptoms).slice(0, 5));

        // check if matching symptoms more than 0
        if(Object.keys(slicedData).length > 0) {

            // iterate through each and add them into suggestion container
            Object.entries(slicedData).forEach(([key, value]) => {

                // preparing suggestion element
                let suggestion = $('<button>').addClass('list-group-item list-group-item-action').text(formatDiseaseName(value[1]) + (value[0].trim().toLowerCase().replace(/ /g, '_') === value[1].trim().toLowerCase().replace(/ /g, '_') ? '' : ' ('+  formatDiseaseName(value[0]) +')'));

                // on click event handler for each item
                suggestion.on('click', function() {

                    // clear input and focus
                    $('#symptoms-input').val('').focus();

                    // add into selected symptom container
                    $("#selected-symptoms").append('<li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2 m-0" data-symptom="'+ value[0] +'">'+ formatDiseaseName(value[1]) +'<button type="button" class="close btn-close ms-3" aria-label="Close"></button></li>');

                    // Here you can perform any other action with the selected data
                    suggestionsDiv.empty();
                });

                // insert into container
                suggestionsDiv.append(suggestion);
            });
        } else {

            // add no match placeholder
            let placeholder_suggestion = $('<button>').addClass('list-group-item list-group-item-action').text(js_translations.no_matching_symptom);
            placeholder_suggestion.on('click', function() {

                // clear input
                $('#symptoms-input').val("");
                suggestionsDiv.empty();
            });
            suggestionsDiv.append(placeholder_suggestion);
        }
    });

    // turn on disease detail on and off
    $('#show-disease-details').on('change', function () {
        if($(this).prop('checked')) $('body').addClass('debug');
        else $('body').removeClass('debug');
    });

    // handle step-1 continue
    $("#step-1-continue").on("click", function () {

        // show bg wait
        $('.bg_wait').show();

        // check if at least 2 symptoms selected
        if($("#selected-symptoms li").length < 1) {

            // hide bg wait
            $(".bg_wait").hide();

            // set error message
            $("#step-1-error-message").text(js_translations.select_symptom_suggestion);

            // show error container
            $("#step-1-error").slideDown("slow").delay(3000).slideUp("slow");

            return;
        }

        // get selected symptoms
        let symptoms = [];
        $('#selected-symptoms li').each(function() {
            let symptom = $(this).data('symptom');
            symptoms.push(symptom);
        });

        // continue
        $.ajax({
            type: 'POST',
            url: '/api/v1/predict',
            success: function (response) {

                // check if response contains diseases and symptoms
                if(response.disease && response.symptoms) {

                    // check and determine specialist doctor
                    if(response.disease.length < 1) {

                        // no disease match found so suggest internal medicine
                        $(".preferred-specialist").text(js_translations.internal_medicine);

                        // note container
                        let note = $('#result-no-match-note');

                        // set note
                        note.html((note.text()).replace('::specialist_doctor::', '<strong>'+ js_translations.internal_medicine +'</strong>'));

                        // show note
                        note.show();
                    } else {

                        // add row class to container
                        $('#assessment-result').addClass('contains-diseases');

                        // note container
                        let note = $('#result-match-note');

                        // set note
                        note.html((note.text()).replace('::specialist_doctor::', '<strong>'+ (response.disease[0].Specialist).toLowerCase().replace(/\b\w/g, c => c.toUpperCase()) +'</strong>'));

                        // show note
                        note.show();

                        // set first disease's specialist as suggested specialist
                        $(".preferred-specialist").text(response.disease[0].Specialist);

                        // set predicted probability
                        $('.predicted-probability').text((parseFloat(response.disease[0].Probability) * 100).toFixed(2) + '%');

                        // suggest the first disease as it has the most probability score
                        $('.predicted-disease').text(response.disease[0].Disease);

                        // loop through diseases and prepare
                        $.each(response.disease, function (index, item) {

                            // fill up predicted disease table
                            $("#symptom-prediction-table").append('<tr><td>'+ item.Disease +'</td><td>'+ (parseFloat(item.Probability)*100).toFixed(2) +'%</td><td>'+ item.Specialist +'</td><td><button type="button" class="btn btn-outline-success" data-bs-toggle="collapse" data-bs-target="#disease-description-'+ index +'" aria-expanded="false"><span><i class="fa fa-chevron-down" aria-hidden="false"></i><i class="fa fa-chevron-up" aria-hidden="true"></i></span></button></td></tr><tr id="disease-description-'+ index +'" class="collapse"><td class="p-4" colspan="4">'+ item.Description +'</td></tr>');
                        });
                    }

                    // keep track of severity
                    let severity_tracker= {
                        "Mild": 0,
                        "Moderate": 0,
                        "Severe": 0,
                        "Critical": 0
                    };

                    // loop through symptoms and process the data
                    $.each(response.symptoms, function (index, item) {

                        // keeping track of how many symptom per severity
                        severity_tracker[item.severity] += 1;

                        // fill up symptom severity table
                        $("#symptom-severity-table").append('<tr><td>' + formatDiseaseName(symptoms_list[item.symptom]) + '</td><td class="'+ item.severity.toLowerCase() +'">'+ js_translations[item.severity.toLowerCase()] +'</td></tr>');
                    });

                    // prepare suggestion message
                    let suggestion = getSuggestion(severity_tracker);

                    // set suggestion message
                    $("#result-suggestion").text(suggestion);

                    // check and show special warning message
                    if(response.symptoms.length === 1) $("#special-result-note").show();

                    // check and show special warning message for doctor type
                    if(response.disease.length > 0) $("#symptom-match").show();
                    else $("#symptom-no-match").show();

                    // hide step 1 container
                    $("#assessment-not-feeling-well").hide();

                    // show assessment result container
                    $("#assessment-result").show();
                } else {

                    // set error message
                    $("#step-1-error-message").text(js_translations.server_error);

                    // show error container
                    $("#step-1-error").slideDown("slow").delay(3000).slideUp("slow");
                }
            },
            error: function () {

                // set error message
                $("#step-1-error-message").text(js_translations.server_error);

                // show error container
                $("#step-1-error").slideDown("slow").delay(3000).slideUp("slow");
            },
            dataType: 'json',
            data: {
                symptoms: symptoms,
                threshold: $('#probability-threshold-setting').attr('data-threshold')
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            complete: function(e, status) {

                // hide bg wait
                $(".bg_wait").hide();
            }
        });
    });

    // handle start assessment
    $(".start-assessment").on("click", function(){

        // hide landing content
        $("#landing-content, #assessment-step-1, #assessment-feeling-well, #assessment-not-feeling-well, #assessment-result, #special-result-note, #symptom-match, #symptom-no-match, #result-match-note, #result-no-match-note").hide();

        // empty selected symptoms
        $("#selected-symptoms, #symptom-severity-table, #symptom-prediction-table").html('');

        // add placeholder row for symptom prediction table
        $('#assessment-result').removeClass('contains-diseases');

        //  reset inserted classes in symptom prediction table
        $('#symptom-prediction-table').append('<tr class="predicted-disease-placeholder"><td colspan="4">'+ js_translations.no_disease_note +'</td></tr>');

        // hide landing content
        $("#assessment-content, #assessment-notice").show();
    });

    // handle continue assessment
    $("#continue-assessment").on("click", function () {

        // hide notice
        $("#assessment-notice").hide();

        // show assessment
        $("#assessment-step-1").show();
    });

    // handle feeling well
    $("#feeling-well").on("click", function () {

        // hide step 1
        $("#assessment-step-1").hide();

        // show feeling well container
        $("#assessment-feeling-well").show();
    });

    // handle not feeling well
    $("#not-feeling-well").on("click", function () {

        // hide step 1
        $("#assessment-step-1").hide();

        // show feeling well container
        $("#assessment-not-feeling-well").show();
    });

    // handle country change
    $("#countrySelect").on("change", function () {

        // close off canvas navbar
        $('#offcanvasNavbar').offcanvas('hide');

        // show bg wait
        $('.bg_wait').show();

        // continue
        $.ajax({
            type: "POST",
            url: "/api/v1/country-change",
            success: function (response) {

                // check if response contains diseases and symptoms
                if(response.response) {

                    // check and determine specialist doctor
                    if(response.response && response.response === 'success') {

                        // reload page
                        location.reload();
                    } else if(response.response && response.response === 'failed') {

                        // set toast error message
                        $('#toast-message').text(response.message);

                        // trigger toast
                        showToast('#toast-container');
                    } else {

                        // set toast error message
                        $('#toast-message').text(js_translations.unknown_error);

                        // trigger toast
                        showToast('#toast-container');
                    }
                } else {

                    // set toast error message
                    $('#toast-message').text(js_translations.unknown_error);

                    // trigger toast
                    showToast('#toast-container');
                }
            },
            dataType: 'json',
            data: {change_country_to: $("#countrySelect").find('option:selected').text()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            complete: function(e, status) {

                // hide bg wait
                setTimeout(function () {
                    $(".bg_wait").hide();
                }, 1500);
            }
        });
    });

    // on click switch to english handler
    $('#switch_to_english').on('click', function () {

        // set country to Canada and trigger change
        $('#countrySelect').val('CA').trigger('change');
    });

    // on click make probability threshold enable
    $('#edit-probability-threshold').on('click', function () {
        $('#probability-threshold').prop('disabled', false);
        $('#probability-threshold-setting').addClass('editing');
    });

    // on click set probability threshold
    $('#set-probability-threshold').on('click', function () {

        // set local value
        let threshold_elm =$('#probability-threshold'),
            container = $('#probability-threshold-setting');

        // check threshold value
        if(!isValidNumber(threshold_elm.val())) {

            // set toast error message
            $('#toast-message').text('Please enter numerical value between 0.01 and 100.00!');

            // show error message in toast
            showToast('#toast-container');

            return;
        }

        threshold_elm.prop('disabled', true);
        container.removeClass('editing');
        container.attr('data-threshold', threshold_elm.val());
    });

    // show toast
    let showToast = function (container, delay = 100) {
        setTimeout(function() {
            $(container).toast('show');
        }, delay);
    }

    // validate valid number
    let isValidNumber = function (value) {

        // First, check if it's a valid number
        if (isNaN(value)) {
            return false;
        }

        // Then, check if it's within the specified range
        let floatValue = parseFloat(value);
        return floatValue >= 0.01 && floatValue <= 100.00;
    }

    // simply format the suggestion string and return it
    let getSuggestion = function (severity_tracker) {

        // suggestion string
        let suggestion = '', count;

        // check and build the suggestion string
        if (severity_tracker.Critical > 0) {
            suggestion = (js_translations.symptom_suggestion_critical).replace('::symptom_count::', severity_tracker.Critical);
            count = severity_tracker.Critical;
        } else if (severity_tracker.Severe > 0) {
            suggestion = (js_translations.symptom_suggestion_severe).replace('::symptom_count::', severity_tracker.Severe);
            count = severity_tracker.Severe;
        } else if (severity_tracker.Moderate > 0) {
            suggestion = (js_translations.symptom_suggestion_moderate).replace('::symptom_count::', severity_tracker.Moderate);
            count = severity_tracker.Moderate;
        } else {
            suggestion = (js_translations.symptom_suggestion_mild).replace('::symptom_count::', severity_tracker.Mild);
            count = severity_tracker.Mild;
        }

        suggestion = suggestion.replace(/::symptom_or_symptoms::/g, count > 1 ? (js_translations.symptoms).toLowerCase() : (js_translations.symptom).toLowerCase());
        suggestion = suggestion.replace(/::is_or_are::/g, count > 1 ? js_translations.are : js_translations.is);

        return suggestion;
    }

    // simply format disease name
    let formatDiseaseName = function (name) {
        // Remove underscores and capitalize each word
        return name.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }
});
