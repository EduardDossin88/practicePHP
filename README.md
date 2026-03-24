# CSV Data Processor & API

Бэкенд-приложение для импорта данных из объемных CSV-файлов на PHP с архитектурой MVC



## Стек технологий
* **PHP 8.5** (разделен на FPM для веба и CLI для фоновых задач)
* **PostgreSQL 15**
* **Nginx** (настроен HTTPS с самоподписанными сертификатами)
* **Docker & Docker Compose** (Multistage сборка)
* **Make** (управление через Makefile)
* **PHPStan & PHP CS Fixer** (контроль качества кода)

## Архитектурные решения
1. **Единая точка входа (Front Controller):** Все запросы обрабатываются через единый маршрутизатор в `public/index.php`.
2. **Паттерн Singleton:** Подключение к БД реализовано через (`Database::getInstance()`), что гарантирует создание лишь одного соединения за время выполнения скрипта.
3. **Безопасность и Валидация:** Трафик проксируется через HTTPS (порт 443). Файлы строго проверяются на расширение и размер, а SQL-запросы используют подготовленные выражения (Prepared Statements) PDO для защиты от инъекций.
4. **Автоматизация развертывания:** Все команды в `Makefile`, `pre-commit` hook, который не позволит закоммитить код с ошибками.

---

## Быстрый старт (Установка и запуск)

Убедитесь, что у вас установлены **Docker**, **Compose** и утилита **Make**.

**1. Сгенерируйте локальные SSL-сертификаты:**
```bash
make cert
```
(На Windows без утилиты openssl, сертификаты можно сгенерировать через Docker: `docker run --rm -v ${PWD}/certs:/certs alpine sh -c "apk add --no-cache openssl && openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /certs/localhost.key -out /certs/localhost.crt -subj '/CN=localhost'"`)

**2. Соберите образы:**
```bash
make build
```

**3. Поднимите контейнеры (БД, Nginx, PHP-FPM) в фоновом режиме:**
```bash
make up
```

---

## API Эндпоинты
Приложение работает через REST-подобное API. Все ответы возвращаются в формате JSON.
1. Веб-интерфейс и загрузка
   * GET / — Возвращает HTML-форму для ручной загрузки CSV-файла.
   * POST /parse — Принимает CSV-файл (до 5 МБ) через multipart/form-data, валидирует его и импортирует в БД.
2. Генерация тестовых данных
   * GET /generate?count=100 — Генерирует указанное количество фейковых пользователей и сохраняет их в data.csv. По умолчанию создает 10 строк. Защищено от отрицательных чисел и переполнения.
3. Поиск и Анализ (Range Search)
   * GET /analyze — Эндпоинт для гибкого поиска по базе данных.

## Поддерживаемые фильтры (можно комбинировать):

* **country, city, gender (строки)** — точный поиск.

* **is_active (1 или 0)** — статус активности.

* **birth_date_from / birth_date_to (YYYY-MM-DD)** — поиск по диапазону дат рождения.

* **registration_date_from / registration_date_to** — поиск по диапазону дат регистрации.

**Пример запроса:**

```Bash
curl -k -X GET "https://localhost/analyze?country=RU&is_active=1&birth_date_from=1990-01-01&birth_date_to=2000-12-31"
```
**Пример ответа**:
```bash
JSON
{
"success": true,
"total_found": 1,
"data": [
{
"id": 42,
"country": "RU",
"city": "Moscow",
"gender": "male",
"salary": 120000,
"first_name": "Ivan",
"last_name": "Ivanov",
"birth_date": "1995-08-20",
"registration_date": "2023-10-01",
"is_active": 1
    }
  ]
}
```
---

## Консольные команды и Качество кода


Для выполнения задач внутри контейнеров используйте 'Make':

* **make console cmd="import"** — Консольный запуск импорта из файла data/data.csv напрямую в БД (минуя веб-сервер).

* **make format** — Запуск автоматического форматирования кода (PHP CS Fixer).

* **make analyze** — Запуск статического анализатора (PHPStan).

* **make down** — Остановка и удаление всех запущенных контейнеров.