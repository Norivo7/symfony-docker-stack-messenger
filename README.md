# TaskManager

Recruitment task implemented in Symfony 7 using a modular DDD-inspired structure, CQRS via Symfony Messenger, Doctrine ORM, Docker and external user import from JSONPlaceholder.

## Features

### User
- import users from JSONPlaceholder
- simplified login by email
- current user endpoint

### Task
- create task with title, description and optional assignee
- assign task to user
- update task status (`todo`, `in_progress`, `done`)
- list tasks
- show task details
- task event history

## Architecture

The project is split into modules:
- `Task`
- `User`
- `Shared`
- `Ping`

Each module is organized into layers:
- `Domain`
- `Application`
- `Infrastructure`
- `Presentation`

CQRS is implemented using Symfony Messenger commands and queries.

## Design patterns

- **Factory Pattern**
    - `TaskFactory`
    - `TaskSearchCriteriaFactory`

- **Strategy Pattern**
    - task status update strategies used by `ChangeTaskStatusHandler`

## Event history

Task changes generate domain events:
- `TaskCreatedEvent`
- `TaskStatusUpdatedEvent`

Events are stored in the `task_events` table and can be read through the history endpoint.

## Authentication

Authentication is intentionally simplified for recruitment-task purposes:
- `POST /auth/login`
- `GET /me` with `X-User-Id` header

## Endpoints

### User
- `POST /users/import`
- `POST /auth/login`
- `GET /me`

### Task
- `GET /tasks`
- `POST /tasks`
- `GET /tasks/{id}`
- `PATCH /tasks/{id}/assign`
- `PATCH /tasks/{id}/status`
- `GET /tasks/{id}/history`

## Run locally

```bash
docker compose up -d --build
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

### Application:

http://localhost:8080

### Tests

```bash
docker compose exec php php bin/phpunit
```

