# Task Management Laravel API

## Backend setup
1. Install dependencies:
   ```bash
   composer install
   ```
2. Configure environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Run migrations + seed data:
   ```bash
   php artisan migrate --seed
   ```
4. Start API server:
   ```bash
   php artisan serve
   ```

## API endpoints
- `POST /api/projects` – create a project
- `GET /api/projects` – list projects with aggregate task stats
- `POST /api/projects/{id}/tasks` – create a task
- `PATCH /api/tasks/{id}` – update task status

## Scheduling algorithm
`TaskScheduler::getNextTasks(User $user)` sorts assigned tasks by:
1. Overdue first (`todo` tasks where `created_at + 3 days` or explicit `due_date` is before today)
2. Higher priority first
3. If both due today and tied, higher project importance first (`project.tasks_count`)
4. Oldest task first as stable fallback

`TaskAlgorithm::topTasks(array $tasks, int $n)` applies the same core ranking logic to plain arrays and returns top `N` tasks.

## Seeded data
`DatabaseSeeder` creates:
- 3 users
- 3 projects
- 20 tasks distributed across projects with mixed status/priority

## Running tests
### PHPUnit
```bash
php artisan test
```

Or run specific unit suites:
```bash
./vendor/bin/phpunit tests/Unit/TaskSchedulerTest.php
./vendor/bin/phpunit tests/Unit/TaskAlgorithmTest.php
```

## Frontend
Frontend dashboard + Vitest are out of scope for this submission (request limited to Laravel API + unit tests).
