---
name: dev-workflow-setup
description: Use when starting work on a new task or issue, creating the branch/worktree, setting up the test database.
---

# Dev Workflow — Setup

## Branch and worktree

Use the native `EnterWorktree` tool with a `name` matching this convention:
`<issue_type>-<issue_number>-<issue_slug>` (e.g. `feat-123-add-blog-rss-feed`).
If you are not working on an issue, skip the issue number, but still generate a meaningful slug.  
The issue slug must always consist of a clear title of the task/issue.

ALWAYS place the worktree in `/var/www/html/.claude/worktrees`.

1. `code --add <worktree_path>` to add the worktree to the VS Code workspace (non-blocking: skip if `code` is unavailable).
2. `cp .env.example .env` if `.env` is missing.
3. `npm install --dev`
4. `composer install --dev`
5. `php artisan key:generate`
6. `npm run build`

## Test database

**Never use the development DB for tests** — always provision one scoped to this worktree.

1. Create `.env.testing`: `cp .env.example .env.testing`.
2. Reserve a database name — see [reserve-db-name.md](reserve-db-name.md), purpose `test`.
3. Write it into `.env.testing`:
   ```bash
   sed -i \
       -e 's/^APP_ENV=.*/APP_ENV=testing/' \
       -e "s/^DB_DATABASE=.*/DB_DATABASE=<db_name>/" \
       .env.testing
   ```
4. Update both the `<env name="DB_DATABASE">` and `<server name="DB_DATABASE">` entries of `phpunit.xml` to point to the test DB (do not commit this change).
