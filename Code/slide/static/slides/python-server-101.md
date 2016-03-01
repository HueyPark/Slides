# Python과 함께
## 서버 기초

---

### 박재완
#### jaewan.huey.park@gmail.com
#### NextFloor 풀스택 게임 프로그래머

---

## 서버란 무엇인가?
### 클라이언트에게 네트워크를 통해 서비스를 제공하는 컴퓨터 또는 프로그램 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
#### -위키백과 <!-- .element: class="fragment fade-in" data-fragment-index="1" style="text-align:right;" -->

---

## 서버의 종류

- web server <!-- .element: class="fragment fade-in" -->
- application server <!-- .element: class="fragment fade-in" -->
- database <!-- .element: class="fragment fade-in" -->
- domain name server <!-- .element: class="fragment fade-in" -->
- file server <!-- .element: class="fragment fade-in" -->
- 등등등 <!-- .element: class="fragment fade-in" -->

---

## 오늘 이야기할 서버
### APPLICATION SERVER <!-- .element: class="fragment fade-in" -->

---

## Application server란 무엇인가?
### 인터넷 상에서 HTTP를 통해 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
### 사용자 컴퓨터나 장치에 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
### 애플리케이션을 수행해주는 미들웨어 <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
#### - 위키백과 <!-- .element: class="fragment fade-in" data-fragment-index="1" style="text-align:right;" -->

---

## Application server의 위치?

### Web server
### |
### Application server <!-- .element: class="fragment highlight-blue"-->
### |
### Database server

---

## Application server는 무엇으로 만들 수 있는가?

- Python <!-- .element: class="fragment fade-in" -->
- Java <!-- .element: class="fragment fade-in" -->
- Javascript <!-- .element: class="fragment fade-in" -->
- Scala <!-- .element: class="fragment fade-in" -->
- 등등등 <!-- .element: class="fragment fade-in" -->

---

## 왜 Python이죠?

- 팀에서 쓸 거 같습니다 ;; <!-- .element: class="fragment fade-in" -->
- 생산성이 좋은 거 같습니다 ;; <!-- .element: class="fragment fade-in" -->

---

## Python
### Python is a programming language that lets you work more quickly and integrate your systems more effectively. <!-- .element: class="fragment fade-in" -->

---

## Python for games
###"Python enabled us to create EVE Online, a massive multiplayer game, in record time. The EVE Online server cluster runs over 50,000 simultaneous players in a shared space simulation, most of which is created in Python. The flexibilities of Python have enabled us to quickly improve the game experience based on player feedback" <!-- .element: class="fragment fade-in" data-fragment-index="1" -->
#### said Hilmar Veigar Petursson of CCP Games. <!-- .element: class="fragment fade-in" data-fragment-index="1" style="text-align:right;" -->

---

## Python 살펴보기

---

### C++

```cpp
int doSomething() {
    auto a = getA();
a.do();
}
```

---

### C++

```cpp
int doSomething() {
    auto a = getA();
    a.do();
}
```

---

### C++

```cpp
int doSomething()
{
    auto a = getA();
    a.do();
}
```

---

### Python

```python
def do_something:
    a = gat_a()
    a.do()
```

---

### C++

```cpp
if (x > 0 && x < 10)
{
    doSomething();
}
```

---

### C++

```cpp
if (0 < x && x < 10)
{
    doSomething();
}
```

---

### Python

```python
if 0 < x < 10:
    do_something()
```

---

## Python 첫인상

- 간결하다 <!-- .element: class="fragment fade-in" -->
- 개발 속도가 빠르다 <!-- .element: class="fragment fade-in" -->

---

## Hello World

```python
print('Hello World')
```

---

## Flask
### Flask is a microframework for Python based on Werkzeug, Jinja 2 and good intentions. <!-- .element: class="fragment fade-in" -->

---

## Flask Hello World
```python
from flask import Flask
app = Flask(__name__)

@app.route('/')
def hello():
    return 'Hello World!'

app.run()
```

---

## PyMySQL
### pure-Python MySQL client library <!-- .element: class="fragment fade-in" -->

---

```python
import pymysql

connection = pymysql.connect(host='localhost',
                             user='user',
                             password='passwd',
                             db='db',
                             charset='utf8mb4',
                             cursorclass=pymysql.cursors.DictCursor)

with connection.cursor() as cursor:
    sql = "INSERT INTO `users` (`email`, `password`) VALUES (%s, %s)"
    cursor.execute(sql, ('webmaster@python.org', 'very-secret'))

connection.commit()
connection.close()
```

---

## SQLAlchemy
### SQLAlchemy is the Python SQL toolkit and Object Relational Mapper that gives application developers the full power and flexibility of SQL <!-- .element: class="fragment fade-in" -->

---

## PyJWT
### Python library which allows you to encode and decode JSON Web Tokens (JWT) <!-- .element: class="fragment fade-in" -->

---

## JSON Web Token
### JSON Web Tokens are an open, industry standard method for representing claims securely between two parties. <!-- .element: class="fragment fade-in" -->

---

## Token based authentication

---

## 누가 사용하나요?
### Facebook, Twitter, Google+, Github ... <!-- .element: class="fragment fade-in" -->

---

## 또 어떤 방법이 있죠?
### Session based authentication

---

### Session based authentication의 동작
1. 유저 -> 서버: 로그인, 서버는 유저정보를 session에 저장 <!-- .element: class="fragment fade-in" -->
2. 유저 <- 서버: session id <!-- .element: class="fragment fade-in" -->
3. 유저 -> 서버: 요청에 session id를 포함, 서버는 유저정보를 확인 후 응답 <!-- .element: class="fragment fade-in" -->

---

### Session based authentication의 문제점
- Sessions <!-- .element: class="fragment fade-in" -->
    - 유저가 인증할 때마다 서버는 어딘가에 인증정보를 저장해야 함
    - 많은 유저와 session을 유지해야 한다면 서버에 부하로 작용
- Scalability <!-- .element: class="fragment fade-in" -->
    - 만약 session 정보가 메모리에 있다면 확장에 문제를 일으킴
    - 공유되는 메모리의 한계가 서버 확장의 한계로 작용

---

### Token based authentication의 동작
1. 유저 -> 서버: 로그인, 서버는 유저정보를 기반으로 암호화된 token 생성 <!-- .element: class="fragment fade-in" -->
2. 유저 <- 서버: token을 클라이언트에 전달 <!-- .element: class="fragment fade-in" -->
3. 유저 -> 서버: 요청에 token을 포함, 서버는 token을 통해 유저정보 확인<!-- .element: class="fragment fade-in" -->

---

### 아직 잘 모르겠어요. 뭐가 다르죠?

---

## Token!
### 토큰이 유저정보를 가지고 있습니다! <!-- .element: class="fragment fade-in" -->

---

## Token과 함께 해서 가능한 일

- Stateless! Scalable! <!-- .element: class="fragment fade-in" -->
- 권한의 전달 (암호 없이!) <!-- .element: class="fragment fade-in" -->
    - 다른 이에게 권한을 전달하는 안전한 방법 (예: Facebook 글쓰기 등)