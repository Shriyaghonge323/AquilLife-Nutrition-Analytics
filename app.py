from flask import Flask, request, jsonify
from flask_cors import CORS
import oracledb

app = Flask(__name__)
CORS(app)

# Database Config - Replace 'your_password' with your Oracle password
db_config = {
    "user": "   ",
    "password": "your_password",
    "dsn": "localhost:1521/orcl" # Use /xe if you have Express Edition
}

def get_db_connection():
    return oracledb.connect(
        user=db_config["user"],
        password=db_config["password"],
        dsn=db_config["dsn"]
    )

@app.route('/signup', methods=['POST'])
def signup():
    data = request.json
    email, password = data.get('email'), data.get('password')
    conn = get_db_connection()
    try:
        cursor = conn.cursor()
        cursor.execute("INSERT INTO SYSTEM.AQUIL_USERS (EMAIL, PASSWORD) VALUES (:1, :2)", [email, password])
        conn.commit()
        return jsonify({"success": True, "message": "User created successfully!"}), 201
    except oracledb.IntegrityError:
        return jsonify({"success": False, "message": "Email already registered"}), 400
    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500
    finally:
        conn.close()

@app.route('/login', methods=['POST'])
def login():
    data = request.json
    email, password = data.get('email'), data.get('password')
    conn = get_db_connection()
    try:
        cursor = conn.cursor()
        cursor.execute("SELECT PASSWORD FROM SYSTEM.AQUIL_USERS WHERE EMAIL = :1", [email])
        result = cursor.fetchone()
        if result and result[0] == password:
            return jsonify({"success": True, "message": "Welcome back!"})
        return jsonify({"success": False, "message": "Invalid email or password"}), 401
    finally:
        conn.close()

if __name__ == '__main__':
    app.run(port=5000, debug=True)