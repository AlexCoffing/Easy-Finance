import random
import json
import pickle
import nltk
import numpy as np
import mysql.connector
from flask import Flask, request, jsonify
from flask_cors import CORS
from tensorflow.keras.models import load_model # type: ignore
from tensorflow.keras.optimizers import SGD # type: ignore
from nltk.stem import WordNetLemmatizer

# Inicializar el Flask y el chatbot
app = Flask(__name__)
CORS(app)

# Cargar el modelo y datos del chatbot
lemmatizer = WordNetLemmatizer()
intents = json.loads(open('intents.json', encoding='utf-8').read())
words = pickle.load(open('words.pkl', 'rb'))
classes = pickle.load(open('classes.pkl', 'rb'))
model = load_model('chatbot_model.h5')

# Compilar el modelo
model.compile(optimizer=SGD(learning_rate=0.01, decay=1e-6, momentum=0.9, nesterov=True), 
              loss='categorical_crossentropy', metrics=['accuracy'])

# Configuración de la conexión a MySQL
db_config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'chatbot'
}

# Función para conectar y consultar el balance de un usuario
def get_user_balance(user_id):
    connection = mysql.connector.connect(**db_config)
    cursor = connection.cursor(dictionary=True)
    cursor.execute("SELECT balance_total FROM datos WHERE id = %s", (user_id,))
    result = cursor.fetchone()
    cursor.close()
    connection.close()
    if result:
        return result['balance_total']
    return None

# Función para clasificar la intención del usuario
def clean_up_sentence(sentence):
    sentence_words = nltk.word_tokenize(sentence)
    sentence_words = [lemmatizer.lemmatize(word.lower()) for word in sentence_words]
    return sentence_words

def bag_of_words(sentence):
    sentence_words = clean_up_sentence(sentence)
    bag = [0] * len(words)
    for w in sentence_words:
        for i, word in enumerate(words):
            if word == w:
                bag[i] = 1
    return np.array(bag)

def predict_class(sentence):
    bow = bag_of_words(sentence)
    res = model.predict(np.array([bow]))[0]
    max_index = np.where(res == np.amax(res))[0][0]
    category = classes[max_index]
    return category

# Función para obtener la respuesta del bot con datos personalizados
def get_bot_response(tag, intents_json, user_id=None):
    if tag == "consultar_balance" and user_id:  # Verifica la intención de balance
        balance = get_user_balance(user_id)
        if balance is not None:
            response = random.choice([response.replace('[balance]', f"${balance}") for response in intents_json['intents'][4]['responses']])
            return response
        else:
            return "No encontré tu información de balance."
    else:
        list_of_intents = intents_json['intents']
        for i in list_of_intents:
            if i['tag'] == tag:
                return random.choice(i['responses'])
    return "Lo siento, no entiendo tu pregunta."

# Endpoint para recibir consultas y devolver respuestas
@app.route('/get_response', methods=['POST'])
def get_response():
    data = request.get_json()
    message = data.get("message")
    user_id = data.get("user_id")  # Suponiendo que el ID del usuario se pasa en la solicitud

    tag = predict_class(message)
    response = get_bot_response(tag, intents, user_id=user_id)
    return jsonify({"response": response})

if __name__ == "__main__":
    app.run(port=5000)
