# FX Tracker — Currency Exchange Rates Microservice

Микросервис для трекинга валютных курсов:

- хранит валютные пары и историю курсов
- синхронизирует курсы с внешним API
- предоставляет JSON API
- реализован на PHP 8.3, Symfony 7.3, PostgreSQL

Архитектура построена по принципам Clean Architecture (Domain / Application / Infrastructure / Presentation).

## Запуск приложения

### Требования

Docker 20+
Docker Compose v2
GNU Make

### Dev-окружение

Перед запуском создайте файл `.env.local` в корне проекта с переменной `FREECURRENCYAPI_API_KEY`.
Для prod окружения задайте переменные для `docker compose` в файле `/docker/.env.prod.compose`

Запуск всех контейнеров (dev режим):
```
make up
```

Запуск всех контейнеров (prod режим):
```
make ENV=prod up
```

Остановка контейнеров:
```
make down
```

### Миграции базы данных

Применить миграции:
```
make migrate
```

### Shell в контейнере

Открыть shell в app-контейнере:
```
make sh
```

## Консольные команды микросервиса

Добавление валютной пары
```
php bin/console app:pair:add USD EUR
```

Удаление валютной пары
```
php bin/console app:pair:remove USD EUR
```

Ручная синхронизация курсов
```
php bin/console app:rates:sync
```

Команда:
– проходит по всем валютным парам
– получает актуальный курс у FreeCurrencyAPI
– сохраняет курс в историю

## JSON API

Основной endpoint:
```
GET /api/v1/rates
```

Пример:
http://localhost:8080/api/v1/rates?base=USD&target=EUR

Параметры:
- `base` — обязательный (например USD)
- `target` — обязательный (например EUR)
- `at` — опциональный ISO8601 datetime

Пример успешного ответа:
```
{
    "base": "USD",
    "target": "EUR",
    "rate”: 0.92310000,
    "valid_at": "2025-01-01T12:00:00+00:00",
    "fetched_at": "2025-01-01T12:00:02+00:00",
    "provider": "freecurrencyapi"
}
```

Ошибка — курс не найден (404):
```
{
    "error": "Rate not found"
}
```

## Scheduler

Отдельный контейнер запускает синхронизацию каждые 60 секунд:
```
php bin/console app:rates:sync –env=prod
```
