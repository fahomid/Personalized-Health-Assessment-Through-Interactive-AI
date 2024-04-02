import joblib
import pandas as pd
from sklearn.exceptions import NotFittedError


# loads ML model and predicts
def load_and_predict(symptoms, threshold=0.2):
    # loading model and label encoder
    try:
        model = joblib.load("random_forest_model.joblib")
        le = joblib.load("label_encoder.joblib")
        feature_names = joblib.load("feature_names.joblib")  # Adjust this based on how feature names were stored
    except FileNotFoundError:
        raise NotFittedError("Model files not found. Please train the model first.")

    # ensuring at least one valid symptom is present
    valid_symptoms = set(feature_names)
    if not set(symptoms).intersection(valid_symptoms):
        return []

    # creating prediction DataFrame (handle missing symptoms gracefully)
    data = {col: 1 if col in symptoms else 0 for col in feature_names}
    test_df = pd.DataFrame(data, index=[0])  # Ensure DataFrame has a single row

    # predicting diseases and probabilities
    try:
        probabilities = model.predict_proba(test_df)
        predictions = le.inverse_transform(model.classes_)
        predicted_diseases = [{"Disease": disease, "Probability": prob} for disease, prob in
                              zip(predictions, probabilities[0])]

        # filtering predictions based on the threshold
        filtered_predictions = [prediction for prediction in predicted_diseases if
                                prediction["Probability"] >= threshold]

        # sorting filtered predictions by probability in descending order
        filtered_predictions.sort(key=lambda x: x["Probability"], reverse=True)
    except NotFittedError:
        return []

    # loading doctor versus disease data (assuming CSV format)
    doc_data = pd.read_csv("data/Doctor_Versus_Disease_modified.csv", encoding="latin1")

    # loading disease description data (assuming CSV format)
    des_data = pd.read_csv("data/Disease_Description.csv", encoding="latin1")

    result_rows = []

    for prediction in filtered_predictions:
        disease = prediction["Disease"]

        # retrieving specialist information
        specialist_info = get_specialist_info(doc_data, disease)

        # retrieving disease description
        description_info = get_description_info(des_data, disease)

        # creating a row for the result DataFrame
        result_row = {
            "Disease": disease,
            "Probability": prediction["Probability"],
            "Specialist": specialist_info,
            "Description": description_info if description_info else "Not available"
        }

        result_rows.append(result_row)

    # converting into dataframe
    filtered_result_df = pd.DataFrame(result_rows)

    # returning as json
    return filtered_result_df.to_dict(orient="records")


# gets description of a disease
def get_description_info(des_data, disease):
    # retrieving description information from the loaded DataFrame
    description_info = des_data[des_data["Disease"] == disease]["Description"].values
    return description_info[0] if len(description_info) > 0 else None


# gets specialist doctor of a disease
def get_specialist_info(doc_data, disease):
    # retrieving specialist information from the loaded DataFrame
    specialist_info = doc_data[doc_data["Disease"] == disease]["Specialist"].values
    return specialist_info[0] if len(specialist_info) > 0 else "Not available"


# main execution
if __name__ == "__main__":
    symptoms = ["itching"]
    result_df = load_and_predict(symptoms, 0.2)
    print(result_df)
