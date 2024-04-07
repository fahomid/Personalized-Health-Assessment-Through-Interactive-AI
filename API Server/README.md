# API Server

## Overview

This section contains the API server portion of the health assessment tool.

## System Requirements

Before running this application, ensure you have Python installed on your system.

## How to run the server

1. Install the required Python packages by running:

   ```bash
   pip install flask pandas scikit-learn joblib
   ```

2. Place the required model files (random_forest_model.joblib, label_encoder.joblib, and feature_names.joblib) in the same directory as the script.
3. Make sure you have the following CSV files in a directory named data:
>  + Doctor_Versus_Disease_modified.csv
>  + Disease_Description.csv
>  + Symptom-severity.csv
4. Run the script. This will start the Flask web server locally.
   ```bash
   python server.py
   ```
5. Once the server is running, you can make POST requests to http://localhost:8081/predict with JSON data containing a list of symptoms and an optional threshold value. The server will respond with JSON data containing disease predictions and severity information for the symptoms provided.

# Model Training
You are free to use the train.py file as per your requirement to generate the models. The dataset used to train the models can be found here: https://www.kaggle.com/datasets/ebrahimelgazar/doctor-specialist-recommendation-system

# Additional Notes
This section contains only the API server portion of the project. To utilize the full system, including the web server, please refer to the README.md file in the Web Server folder for instructions on setting it up and integrating it with the Laravel application.

# License
This project is licensed under the Apache License 2.0 - see the LICENSE file for details.
