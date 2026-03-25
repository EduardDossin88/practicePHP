# Роль и Контекст
Ты — Senior PHP Backend Developer. Твоя задача — написать с нуля приложение для импорта, генерации и анализа данных из объемных CSV-файлов.
Код должен быть production-ready, строго типизированным и покрытым комментариями PHPDoc.

# Стек технологий
- PHP 8.5 (FPM + CLI)
- PostgreSQL
- Nginx
- Docker & Docker Compose
- Управление через Makefile
- PHPStan (level 5) и PHP CS Fixer

# Архитектура проекта (Строгий MVC без фреймворков)
Тебе нужно создать следующую структуру папок и файлов:
/public
  - index.php (Front Controller, единая точка входа)
/src
  /Controllers (AnalyzerController, GeneratorController, ParserController)
  /Models (BaseModel, User)
  /Commands (ImportCommand, GenerateCommand)
  /Services (Database)
  /Core (Router)
/data
  - data.csv (сюда генерируются и отсюда читаются файлы)
/config
  - phpstan.neon
  - .php-cs-fixer.dist.php

# Главные технические требования (ОБЯЗАТЕЛЬНО К ИСПОЛНЕНИЮ):

1. **База данных и Модели:**
- Реализовать паттерн Singleton для подключения к БД (`Database::getInstance()`).
- Создать абстрактный класс `BaseModel` с методами `all()` и `findByFilters(array $filters)`.
- Метод `findByFilters` ОБЯЗАН поддерживать поиск по диапазону дат. Если в фильтре передан массив с ключами `from` и `to` (например, для `birth_date`), SQL-запрос должен использовать `>=` и `<=`. Защита от SQL-инъекций через Prepared Statements (PDO) обязательна!
- Создать `final class User extends BaseModel`. Свойства строго в `snake_case` (`is_active`, `birth_date`, `has_children` и т.д.). Метод маппинга `create(array $data): self`.

2. **Роутинг и Контроллеры:**
- Вся маршрутизация идет через `public/index.php`.
- `GET /` — отдает простую HTML-форму для загрузки CSV.
- `POST /parse` — принимает файл, проверяет (только .csv, до 5 МБ). При успехе вызывает `ImportCommand`. Весь процесс обернут в `try-catch`. При ошибке возвращает HTTP 500 и JSON с описанием. При успехе HTTP 200 и JSON `{"success": true}`.
- `GET /generate?count=X` — вызывает `GenerateCommand`.
- `GET /analyze` — вызывает `AnalyzerController`, который читает `$_GET` и передает фильтры в `User::findByFilters()`. Возвращает JSON.

3. **CLI Команды:**
- `GenerateCommand`: использует библиотеку `FakerPHP/Faker`. Генерирует CSV. ВАЖНО: булевы значения (`is_active`, `has_children`) должны записываться в CSV как числа `1` и `0` (а не 'true'/'false'). Заголовки колонок в CSV должны быть в `snake_case`.

4. **Инфраструктура:**
- Напиши базовый `Makefile` с командами: `up`, `down`, `build`, `format` (запуск cs-fixer), `analyze` (запуск phpstan), `import`.