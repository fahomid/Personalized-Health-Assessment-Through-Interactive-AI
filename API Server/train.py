import joblib
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.metrics import accuracy_score, precision_score, recall_score, f1_score


def train_and_save_model():

    # loading data
    dis_sym_data = pd.read_csv("data/dataset.csv")

    # extracting symptom columns
    columns_to_check = [col for col in dis_sym_data.columns if col != 'Disease']

    # identifying unique symptoms
    symptoms = dis_sym_data.iloc[:, 1:].values.flatten()
    symptoms = list(set(symptoms))

    # concatenating all columns at once using pd.concat
    dis_sym_data_v1 = pd.concat([dis_sym_data, pd.DataFrame(columns=symptoms)])

    # converting symptom presence into binary features
    for symptom in symptoms:
        dis_sym_data_v1[symptom] = dis_sym_data_v1.iloc[:, 1:].apply(
            lambda row: int(symptom in row.values), axis=1)

    # removing unnecessary columns and clean column names
    dis_sym_data_v1 = dis_sym_data_v1.drop(columns=columns_to_check)
    dis_sym_data_v1 = dis_sym_data_v1.loc[:, dis_sym_data_v1.columns.notna()]
    dis_sym_data_v1.columns = dis_sym_data_v1.columns.str.strip()

    # encoding target variable (Disease) using Label Encoder
    var_mod = ['Disease']
    le = LabelEncoder()
    for i in var_mod:
        dis_sym_data_v1[i] = le.fit_transform(dis_sym_data_v1[i])

    # splitting data into features (X) and target (y)
    X = dis_sym_data_v1.drop(columns="Disease")
    y = dis_sym_data_v1['Disease']

    # Splitting the data into training and testing sets
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=77)

    # defining and fitting the Random Forest model
    random_forest_model = RandomForestClassifier()
    random_forest_model.fit(X_train, y_train)

    # predicting on the test set
    y_pred = random_forest_model.predict(X_test)

    # calculating accuracy
    accuracy = accuracy_score(y_test, y_pred)
    print("Accuracy:", accuracy)

    # adding additional evaluation metrics
    precision = precision_score(y_test, y_pred, average='weighted')
    recall = recall_score(y_test, y_pred, average='weighted')
    f1 = f1_score(y_test, y_pred, average='weighted')

    # printing scores
    print("Precision:", precision)
    print("Recall:", recall)
    print("F1 Score:", f1)

    # validating model using cross validation
    cv_scores = cross_val_score(random_forest_model, X, y, cv=5)
    print("Cross Validation Scores:", cv_scores)
    print("Mean CV Score:", cv_scores.mean())

    # saving the model, label encoder, and feature names
    joblib.dump(random_forest_model, "random_forest_model.joblib")
    joblib.dump(le, "label_encoder.joblib")
    joblib.dump(list(X.columns), "feature_names.joblib")

    print("Random Forest Model Trained and Saved Successfully!")


if __name__ == "__main__":
    train_and_save_model()
