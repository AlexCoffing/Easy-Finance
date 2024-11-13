import random
import json
import pickle
import numpy as np

import nltk
from nltk.stem import WordNetLemmatizer

from flask import Flask, request, jsonify
from flask_cors import CORS  # Importar CORS
from tensorflow.keras.models import load_model # type: ignore

# Inicializar el Flask y el chatbot
app = Flask(__name__)
CORS(app)  # Habilitar CORS para todas las rutas

lemmatizer = WordNetLemmatizer()
intents = json.loads(open('intents.json').read())
words = pickle.load(open('words.pkl', 'rb'))
classes = pickle.load(open('classes.pkl', 'rb'))
model = load_model('chatbot_model.h5')

# Funciones del chatbot
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

def get_bot_response(tag, intents_json):
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
    tag = predict_class(message)
    response = get_bot_response(tag, intents)  # Usar el nuevo nombre aquí
    return jsonify({"response": response})

if __name__ == "__main__":
    app.run(port=5000)
