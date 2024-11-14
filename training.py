import random
import json
import pickle
import numpy as np
import nltk
from nltk.stem import WordNetLemmatizer
from tensorflow.keras.models import Sequential      # type: ignore
from tensorflow.keras.layers import Dense, Dropout  # type: ignore
from tensorflow.keras.optimizers import SGD         # type: ignore

# Inicializar y descargar recursos de NLTK
lemmatizer = WordNetLemmatizer()
nltk.download('punkt')
nltk.download('wordnet')
nltk.download('omw-1.4')

# Cargar datos de intenciones
intents = json.loads(open('intents.json', encoding='utf-8').read())

# Listas para almacenar datos
words = []
classes = []
documents = []
ignore_words = ['¿', '?', '¡', '!', ',', '.', ':', ';']

# Procesar los datos de las intenciones
for intent in intents['intents']:
    for pattern in intent['patterns']:
        # Tokenizar y lematizar las palabras en cada patrón
        word_list = nltk.word_tokenize(pattern)
        words.extend(word_list)
        documents.append((word_list, intent['tag']))
        if intent['tag'] not in classes:
            classes.append(intent['tag'])

# Lematizar y filtrar palabras, ordenar palabras y clases
words = sorted(set([lemmatizer.lemmatize(w.lower()) for w in words if w not in ignore_words]))
classes = sorted(set(classes))

# Guardar palabras y clases en archivos pickle
pickle.dump(words, open('words.pkl', 'wb'))
pickle.dump(classes, open('classes.pkl', 'wb'))
print("Palabras procesadas y guardadas en words.pkl")
print("Clases procesadas y guardadas en classes.pkl")

# Preparar datos de entrenamiento
training = []
output_empty = [0] * len(classes)

for doc in documents:
    # Crear una bolsa de palabras para cada patrón
    bag = []
    pattern_words = [lemmatizer.lemmatize(word.lower()) for word in doc[0]]
    bag = [1 if w in pattern_words else 0 for w in words]
    
    # Crear la fila de salida
    output_row = list(output_empty)
    output_row[classes.index(doc[1])] = 1
    
    training.append([bag, output_row])

# Mezclar y convertir datos de entrenamiento a un array de Numpy
random.shuffle(training)
training = np.array(training, dtype="object")

# Dividir los datos en inputs (train_x) y outputs (train_y)
train_x = np.array([np.array(x) for x in list(training[:, 0])])
train_y = np.array([np.array(y) for y in list(training[:, 1])])

# Definir el modelo de la red neuronal
model = Sequential()
model.add(Dense(128, input_shape=(len(train_x[0]),), activation='relu'))
model.add(Dropout(0.5))
model.add(Dense(64, activation='relu'))
model.add(Dropout(0.5))
model.add(Dense(len(train_y[0]), activation='softmax'))

# Compilar el modelo
sgd = SGD(learning_rate=0.01, decay=1e-6, momentum=0.9, nesterov=True)
model.compile(loss='categorical_crossentropy', optimizer=sgd, metrics=['accuracy'])

# Entrenar el modelo
train_process = model.fit(train_x, train_y, epochs=350, batch_size=5, verbose=1)

# Guardar el modelo entrenado
model.save('chatbot_model.h5')
print("Modelo guardado en chatbot_model.h5")