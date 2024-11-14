import random
import json
import pickle
import nltk
import numpy as np
import mysql.connector

from flask import Flask, request, jsonify
from flask_cors import CORS

from nltk.stem import WordNetLemmatizer

from tensorflow.keras.models import load_model  # type: ignore
from tensorflow.keras.optimizers import Adam    # type: ignore
from tensorflow.keras.models import Sequential  # type: ignore
from tensorflow.keras.layers import Embedding, LSTM, Dropout, Dense # type: ignore
from tensorflow.keras.preprocessing.sequence import pad_sequences   # type: ignore
from tensorflow.keras.preprocessing.text import Tokenizer           # type: ignore

# Inicializar Flask y el chatbot
app = Flask(__name__)
CORS(app)

# Cargar el modelo y datos del chatbot
lemmatizer = WordNetLemmatizer()
intents = json.loads(open('intents.json', encoding='utf-8').read())
words = pickle.load(open('words.pkl', 'rb'))
classes = pickle.load(open('classes.pkl', 'rb'))

# Tokenización y padding de secuencias
tokenizer = Tokenizer(num_words=len(words))
tokenizer.fit_on_texts(words)
sequences = tokenizer.texts_to_sequences(words)
padded_sequences = pad_sequences(sequences, padding='post')

# Definir el modelo LSTM
model = Sequential()
model.add(Embedding(input_dim=len(words) + 1, output_dim=64))
model.add(LSTM(64, return_sequences=True))
model.add(Dropout(0.5))
model.add(LSTM(32))
model.add(Dropout(0.5))
model.add(Dense(len(classes), activation='softmax'))

# Compilar el modelo
model.compile(loss='categorical_crossentropy', optimizer='adam', metrics=['accuracy'])

# Cargar el modelo entrenado
model = load_model('chatbot_model.h5')

# Configuración de la conexión a MySQL
db_config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'chatbot'
}

# Variable para almacenar el balance anterior de los usuarios
last_balance = {}

# Función para conectar y consultar el balance de un usuario
def get_user_balance(user_id):
    connection = mysql.connector.connect(**db_config)
    cursor = connection.cursor(dictionary=True)
    cursor.execute("SELECT balance_total FROM datos WHERE id = %s", (user_id,))
    result = cursor.fetchone()
    cursor.close()
    connection.close()
    if result:
        current_balance = result['balance_total']
        if user_id in last_balance:
            previous_balance = last_balance[user_id]
            if current_balance < previous_balance * 0.5:
                return current_balance, True
        last_balance[user_id] = current_balance
        return current_balance, False
    return None, False

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
    padded_bow = pad_sequences([bow], padding='post', maxlen=model.input_shape[1])
    res = model.predict(padded_bow)[0]
    ERROR_THRESHOLD = 0.25  # Ajuste de umbral según sea necesario
    results = [[i, r] for i, r in enumerate(res) if r > ERROR_THRESHOLD]

    # Ordenar por fuerza de probabilidad
    results.sort(key=lambda x: x[1], reverse=True)
    return classes[results[0][0]] if results else 'unknown'

# Función para obtener la respuesta del bot con datos personalizados
def get_bot_response(tag, intents_json, user_id=None):
    if tag == "consultar_balance" and user_id:
        balance, alert = get_user_balance(user_id)
        if balance is not None:
            response_template = random.choice(intents_json['intents'][3]['responses'])
            response = response_template.replace("[balance]", f"${balance}")
            if alert:
                response += " ¡Cuidado! Estás gastando mucho. Considera revisar tus finanzas."
            return response
        else:
            return "No encontré tu información de balance."
    else:
        for i in intents_json['intents']:
            if i['tag'] == tag:
                return random.choice(i['responses'])
    return "Lo siento, no entiendo tu pregunta."

# Endpoint para recibir consultas y devolver respuestas
@app.route('/get_response', methods=['POST'])
def get_response():
    data = request.get_json()
    message = data.get("message")
    user_id = data.get("user_id")

    tag = predict_class(message)
    response = get_bot_response(tag, intents, user_id=user_id)
    return jsonify({"response": response})

if __name__ == "__main__":
    app.run(port=5000)