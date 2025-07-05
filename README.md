# Symfony Docker Stack Messenger 

A full-featured Symfony API stack built with Docker, including:

- PHP 8.2 + Symfony 6
- Redis with Messenger (async message queues)
- MySQL & PostgreSQL support
- Varnish HTTP caching layer
- phpMyAdmin & pgAdmin GUI tools

Designed for backend developers who want a modern environment to build and test robust API systems, with async processing, queues, and Dockerized microservices.

---

## 🛠️ Stack

| Layer        | Tool/Service        |
|--------------|---------------------|
| Language     | PHP 8.2             |
| Framework    | Symfony 6           |
| Web server   | Nginx               |
| Cache/Queue  | Redis + Messenger   |
| DB           | MySQL, PostgreSQL   |
| GUI tools    | phpMyAdmin, pgAdmin |
| HTTP Cache   | Varnish             |

---

## 🚀 Getting Started

### 1. Clone this repo
```bash
git clone git@github.com:Norivo7/symfony-docker-stack-messenger.git
cd symfony-docker-stack-messenger
```
### 2. Start the environment
```bash
docker compose up -d --build
```

### 3. Access the app

|       Tool       |         URL         |
-------------------|---------------------|
|  app	           | http://localhost:8080
|  phpMyAdmin	   | http://localhost:8081
|  Varnish         | http://localhost:8082
|  pgAdmin	       | http://localhost:8083

###  4. Enter the PHP container and install dependencies

```   bash
docker compose exec php bash
composer install
```

---

### 🔁 Available Endpoints

| Route         | Method | Description     |
|---------------|--------|-----------------|
| /ping         | GET    | instant message |
| /delayedPing  | GET    | 5000 ms delay   |
| /failedPing   | GET    | failed ping     |


### 🧠 TODO Ideas
- /tasks endpoint + async handling via Messenger
- Varnish cache headers support
- Add JWT authentication
- Add CI/CD pipeline (e.g. GitHub Actions)
- Setup Swagger/OpenAPI
