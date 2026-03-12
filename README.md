# TaskManager

Recruitment task implemented in Symfony 7 using a modular DDD-inspired structure, CQRS via Symfony Messenger, Doctrine ORM, Docker and external user import from JSONPlaceholder.

## Features

### User
-   Import users from JSONPlaceholder API
-   Simplified login using email
-   Endpoint for retrieving the currently authenticated user

### Task
-   Create tasks with title, description and optional assignee
-   Assign tasks to users
-   Update task status (`todo`, `in_progress`, `done`)
-   List tasks with filters
-   View task details
-   View task change history

---

## Architecture

The project is split into modules:
- `Task`
- `User`
- `Shared`
- `Ping`

Each module is organized into layers:
-   **Domain** - business logic and entities
-   **Application** - commands, queries, handlers, strategies
-   **Infrastructure** - Doctrine repositories, external integrations
-   **Presentation** - controllers and HTTP layer

The application follows a CQRS approach using Symfony Messenger.

---

## Design patterns

### Factory Pattern

Factories are used to encapsulate object creation logic.

Examples:
- `TaskFactory` 
- `TaskSearchCriteriaFactory`

### Strategy Pattern

Task status transitions are handled using strategies.\
Each status update is processed by a dedicated strategy implementation.

Used in:
- `ChangeTaskStatusHandler`
- `TaskStatusStrategyResolver`

This keeps the logic extensible and compliant with the Open/Closed
Principle.

---

## Event history

Task changes generate domain events:
- `TaskCreatedEvent`
- `TaskStatusUpdatedEvent`

Events are stored in the `task_events` table and can be read through the history endpoint.

This provides a lightweight event logging mechanism for auditing task changes.

--- 

## Authentication

Authentication is intentionally simplified for recruitment-task purposes:

Flow:

1.  Import users
2.  Login using email
3.  Use returned user id as header

Endpoints:

    POST /auth/login
    GET /me

Header required for `/me`:

    X-User-Id: <user_id>

---

# API Endpoints

## User

### Import users
    POST /users/import
Imports users from JSONPlaceholder into the local database.

---

### Login
    POST /auth/login

Request body:
``` json
{
  "email": "Sincere@april.biz"
}
```

Response:
``` json
{
  "message": "Login successful",
  "userId": 1,
  "email": "Sincere@april.biz",
  "name": "Leanne Graham"
}
```

---

### Current user
    GET /me
Header:

    X-User-Id: 1

---

# Task

### List tasks

    GET /tasks

Optional query parameters:

    status
    createdFrom
    createdTo

Example:

    GET /tasks?status=todo

---

### Create task

    POST /tasks

Request body:

``` json
{
  "title": "Prepare recruitment task",
  "description": "Finish implementation",
  "assignedUserId": null
}
```

---

### Show task

    GET /tasks/{id}

---

### Assign task

    PATCH /tasks/{id}/assign

Request body:

``` json
{
  "userId": 1
}
```

---

### Change task status

    PATCH /tasks/{id}/status

Request body:

``` json
{
  "status": "in_progress"
}
```

Allowed values:

    todo
    in_progress
    done

---

### Task history

    GET /tasks/{id}/history

Returns the list of domain events recorded for the task.

---

## Run Locally

Start the environment:

``` bash
docker compose up -d --build
```

Run database migrations:

``` bash
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

Application will be available at:

    http://localhost:8080

---

# Tests

Run tests with:

``` bash
docker compose exec php php bin/phpunit
```

Current tests cover: 
- `TaskFactory`
- `TaskStatusStrategyResolver`

---
