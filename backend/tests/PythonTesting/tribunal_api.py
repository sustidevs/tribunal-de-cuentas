from flask import Flask, jsonify, request
from time import sleep, time
import requests
import random
import json
import csv

app = Flask(__name__)
PATH = "http://localhost:8000/api"


#  http://localhost:5000/
@app.route('/')
def index():
    return jsonify({
        "message": "Sep, parece que está funcionando",
        "routes": [
            "http://localhost:5000/create_expediente/20237422490/3",
            "http://localhost:5000/create_expediente/20237422490",
            "http://localhost:5000/create_from_csv/20237422490"
        ]
    })


#  http://localhost:5000/create_expediente/20237422490/3
@app.route('/create_expediente/<string:cuil>', methods=['GET'])
@app.route('/create_expediente/<string:cuil>/<string:cantidad>', methods=['GET'])
def create_expediente_by_url(cuil, cantidad=1):
    try:
        session = login(cuil, cuil)
        res = Expediente.create(session, int(cantidad))
        return res
    except Exception as ex:
        return str(ex)


#
@app.route('/create_expediente', methods=['POST'])
def create_expediente(cantidad=1):
    try:
        req = json.loads(request.data)
        if req.get("cantidad") is not None:
            cantidad = req["cantidad"]
        session = login(req["cuil"], req["cuil"])
        return Expediente.create(session, int(cantidad))
    except Exception as ex:
        return str(ex)


# http://localhost:5000/create_from_csv/20237422490
@app.route('/create_from_csv/<string:cuil>')
def create_from_csv(cuil):
    session = login(cuil, cuil)
    # result = Expediente.create_from_csv(session)
    return Expediente.create_from_csv(session)


def login(cuil, password):
    try:
        req_url = PATH + "/login?cuil=" + str(cuil) + "&password=" + str(password)
        response = requests.request("POST", req_url).text
        response = json.loads(response)
        response = {
            "user_id": response["id"],
            "nombre_apellido": response["nombre_apellido"],
            "cuil": response["cuil"],
            "area": response["area"],
            "cargo": response["cargo"],
            "access_token": response["access_token"].split("|")[1]
        }
        print("Logueado como: " + response["nombre_apellido"] + "\tCuil:" + str(response["cuil"]) + "\tToken: " +
              response["access_token"])
        return response
        # return res["token"].split("|")[1]
    except Exception as ex:
        return ex


class User:
    @staticmethod
    def change_password(user_id, password):
        req_url = "http://localhost:8000/api/actualizaPassword?id=" + str(user_id) + "&password=" + str(password)
        headersList = {
            "Accept": "*/*",
            "User-Agent": "Thunder Client (https://www.thunderclient.io)",
            "Content-Type": "application/json"
        }
        payload = "{\n    \"nro_expediente\": \"42221-2510-123122023/2021\", \n    \"nro_fojas\": \"250\", " \
                  "\n    \"prioridad_id\": \"1\",\n    \"tipo_exp_id\": \"1\", \n    \"monto\": \"100\", " \
                  "\n    \"user_id\": \"1\", \n    \"area_id\": \"1\", \n    \"iniciador_id\": \"1\"," \
                  "\n    \"descripcion_extracto\": \"Extracto\"\n} "
        response = requests.request("POST", req_url, data=payload, headers=headersList)

        return response.text


def create_iniciador(token, iniciador):
    try:
        token = str(token)
        url_store_iniciador = PATH + '/store-iniciador'
        headers = {
            "Accept": "*/*",
            "Authorization": "Bearer " + token,
            "Content-Type": "application/json"
        }
        # Para que store-iniciador funcione correctamente se debe deshabilitar las validaciones IniciadorController::store()
        result = requests.post(url_store_iniciador, json=iniciador, headers=headers)
        result = json.loads(result.text)["id"]
        return result
    except Exception as ex:
        print(ex)
        return ex


def find_or_create_iniciador(token, iniciador):
    try:
        token = str(token)
        url_buscar = PATH + '/buscar-iniciador'
        headers = {
            "Accept": "*/*",
            "Authorization": "Bearer " + token,
            "Content-Type": "application/json"
        }
        params = {'nombre': iniciador["nombre"]}

        # Ruta no definida
        response = requests.get(url_buscar, json=params, headers=headers)
        response_json = json.loads(response.text)
        # return create_iniciador(token, iniciador)  # Devuelve el ID del iniciador creado
        if len(response_json) > 0:
            print("Ya existe el iniciador: " + response_json[0]["nombre"])
            return response_json[0]["id"]  # Devuelve el ID del iniciador
        else:
            print("Creando el iniciador: ")
            return create_iniciador(token, iniciador)  # Devuelve el ID del iniciador creado
    except Exception as ex:
        print(ex)


class Expediente:
    @staticmethod
    def create_from_csv(session):
        token = session["access_token"]
        token = str(token)
        url = PATH + '/storeExp'
        headers = {
            "Accept": "*/*",
            "Authorization": "Bearer " + token,
            "Content-Type": "application/json"
        }
        exp_creados = 0
        tiempo_inicio = time()
        path_nroexp = PATH + '/nroExp'
        try:
            with open('./expediente_siifs.csv') as f:
                reader = csv.reader(f)
                for row in reader:
                    print("\nVamo a ver la fila " + str(row[0]))
                    iniciador = {
                        "tipo_entidad": "5",
                        "nombre": row[6],
                        "apellido": row[7],
                        "dni": row[8],
                        "cuil": row[9],
                        "cuit": row[10],
                        "telefono": row[11],
                        "email": row[12],
                        "direccion": row[13],
                        "area_reparticiones": row[14],
                    }
                    id_iniciador = find_or_create_iniciador(token, iniciador)
                    expediente = {
                        "nro_fojas": row[3],
                        "nro_expediente_ext": 6666,  # row[2],
                        "prioridad_id": 2,  # Prioridad Normal
                        "tipo_exp_id": 24,  # SIIF
                        "monto": random.randint(1, 500),
                        "user_id": session["user_id"],
                        "area_id": row[1],
                        "iniciador_id": id_iniciador,
                        "descripcion_extracto": row[5]
                    }
                    response = requests.post(url, json=expediente, headers=headers)
                    print(response.json()[-1])
                    if response.ok:
                        print("Expediente Nro " + str(response.json()[4]) + " creado correctamente.")
                    else:
                        print("El expediente no pudo ser creado.")
                    exp_creados += 1
        except Exception as ex:
            print(ex)
            return ex
        tiempo = time() - tiempo_inicio
        # msg_exp_creados = "\n" + str(exp_creados) + " expedientes creados de " + str(exp_totales) + "."
        msg_tiempo = "Tiempo de ejecución: " + str(round(tiempo, 2)) + " segundos. \n\n"
        # print(msg_exp_creados + "\n" + msg_tiempo)
        return msg_tiempo

    @staticmethod
    def create(session, cantidad=1):
        lista_exp = []
        token = session["access_token"]
        token = str(token)
        url = PATH + '/storeExp'
        headers = {
            "Accept": "*/*",
            "Authorization": "Bearer " + token,
            "Content-Type": "application/json"
        }
        exp_creados = 0
        exp_totales = cantidad
        tiempo_inicio = time()
        for i in range(cantidad):

            nro_fojas = random.randint(2, 999)
            try:
                expediente = {
                    "nro_fojas": nro_fojas,
                    "prioridad_id": random.randint(1, 2),
                    "tipo_exp_id": random.randint(1, 24),
                    "monto": random.randint(1, 500),
                    "user_id": random.randint(1, 115),
                    "area_id": 13,  # random.randint(1, 25),
                    "iniciador_id": random.randint(1, 47),
                    "descripcion_extracto": "Expediente con " + str(nro_fojas) + " fojas."}
                response = requests.post(url, json=expediente, headers=headers)
                nro_expediente = response.json()[4]  # Numero Expediente
                if response.ok:
                    print(str(i + 1) + ". Expediente Nro " + str(nro_expediente) + " creado correctamente.")
                    lista_exp.append(str(nro_expediente))
                else:
                    print(str(i + 1) + ". Expediente Nro " + str(nro_expediente) + " no pudo ser creado.")
                exp_creados += 1
            except:
                # print(str(i + 1) + ". Expediente Nro " + str(nro_expediente) + " no pudo ser creado.")
                pass
        tiempo = time() - tiempo_inicio
        # msg_exp_creados = "\n" + str(exp_creados) + " expedientes creados de " + str(exp_totales) + "."
        # msg_tiempo = "Tiempo de ejecución: " + str(round(tiempo, 2)) + " segundos. \n\n"
        try:
            res = {
                "exp_creados": exp_creados,
                "exp_totales": exp_totales,
                "tiempo": round(tiempo, 2),
                "lista_exp": lista_exp
            }
            return res
        except Exception as ex:
            return ex
        # return msg_exp_creados + "\n" + msg_tiempo

    @staticmethod
    def get():
        url = PATH + '/all-expedientes'
        tiempo_inicio = time()
        headers = {'user-agent': 'Thunder Client (https://www.thunderclient.io)'}
        response = requests.get(url, headers=headers)
        tiempo = time() - tiempo_inicio
        msg_tiempo = "Tiempo de ejecución: " + str(round(tiempo, 2)) + " segundos."
        return response.text + "\n" + msg_tiempo

    # @staticmethod
    # def get_new():
    #     url = PATH + '/all-expedientes_new'
    #     tiempo_inicio = time()
    #     headers = {'user-agent': 'Thunder Client (https://www.thunderclient.io)'}
    #     response = requests.get(url, headers=headers)
    #     tiempo = time() - tiempo_inicio
    #     msg_tiempo = "Tiempo de ejecución: " + str(round(tiempo, 2)) + " segundos."
    #     return response.text + "\n\n" + msg_tiempo

    @staticmethod
    def contarExp():
        url = PATH + '/contarExp'
        tiempo_inicio = time()
        response = requests.post(url)
        tiempo = time() - tiempo_inicio
        msg_tiempo = "\n\nTiempo de ejecución: " + str(round(tiempo, 2)) + " segundos."
        return response.json() + msg_tiempo

    @staticmethod
    def delete():
        pass


if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5000, debug=True)

# docker build -t flaskapp .
# docker run -it --publish 5000:5000 flaskapp
