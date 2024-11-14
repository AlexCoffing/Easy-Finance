import nltk
from nltk.stem import WordNetLemmatizer
import json
import pickle

# Inicializar el lematizador y cargar los datos de intenciones
lemmatizer = WordNetLemmatizer()
intents = json.loads(open('intents.json', encoding='utf-8').read())

words = []
classes = []
documents = []
ignore_words = ['?', '!', '.', ',']

# Recorrer cada intención y preparar los datos
for intent in intents['intents']:
    for pattern in intent['patterns']:
        # Tokenizar cada palabra en la oración
        word_list = nltk.word_tokenize(pattern)
        words.extend(word_list)
        # Añadir documentos a la lista
        documents.append((word_list, intent['tag']))
        # Añadir a la lista de clases
        if intent['tag'] not in classes:
            classes.append(intent['tag'])

# Lematizar y bajar las palabras
words = [lemmatizer.lemmatize(w.lower()) for w in words if w not in ignore_words]
words = sorted(list(set(words)))

# Ordenar las clases
classes = sorted(list(set(classes)))

# Guardar palabras y clases en archivos pickle
pickle.dump(words, open('words.pkl', 'wb'))
pickle.dump(classes, open('classes.pkl', 'wb'))

print("Palabras procesadas y guardadas en words.pkl")
print("Clases procesadas y guardadas en classes.pkl")