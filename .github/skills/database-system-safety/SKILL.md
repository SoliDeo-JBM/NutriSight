---
name: database-system-safety
description: "Protect database and system integrity during development and maintenance. Use when inspecting, changing, migrating, seeding, importing, exporting, or troubleshooting application data, schemas, configuration, jobs, or infrastructure. Require non-destructive, reversible changes and prevent accidental data loss."
argument-hint: "Describe the database or system task and the affected environment."
user-invocable: true
---

# Database and System Safety

## Purpose

Preserve existing data and application behavior while diagnosing or changing database-backed systems. Treat production-like environments and shared development databases as protected unless the user explicitly identifies a disposable environment.

## Safety Rules

- Default to read-only inspection. Do not delete, truncate, drop, reset, overwrite, or bulk-update data.
- Do not modify migrations, seeders, imports, backups, or deployment configuration in a way that wipes or silently rewrites existing data.
- Do not run destructive SQL, framework reset/fresh commands, rollback commands, force pushes, or filesystem cleanup commands.
- Do not expose credentials, connection strings, tokens, or sensitive records in output, logs, patches, or reports.
- Preserve existing application behavior outside the requested change. Avoid broad refactors and unrelated schema changes.
- Prefer additive, idempotent, reversible changes with an explicit rollback path.
- Never assume a local database is disposable. Ask for confirmation before any operation that could mutate shared or persistent state.

## Procedure

1. Identify the target environment, database connection, data sensitivity, requested outcome, and affected application boundary. If the environment or impact is unclear, keep the work read-only and ask a focused question.
2. Inspect the relevant code, schema, migrations, models, logs, and tests without changing state. Use masked or aggregate queries when records may contain personal or sensitive data.
3. Form a narrow hypothesis about the controlling code path and define a cheap check that could disprove it.
4. Choose the smallest non-destructive change. Prefer application-level fixes, validation, dry runs, temporary diagnostics, additive schema changes, and backups or snapshots over direct data mutation.
5. Before editing, state what can change, what must remain unchanged, and how the change can be reversed. Do not edit migrations or seeders merely to repair existing data.
6. Apply the change without destructive commands. For data repair, generate a reviewable script or dry-run report rather than executing it; include transaction boundaries, scope guards, and a rollback plan when relevant.
7. Run focused tests, static checks, schema validation, and a dry run where available. Confirm that unrelated features and existing records remain intact.
8. Review the diff and command history for accidental secrets, destructive statements, broad scope, and unrelated behavior changes. Report any unverified assumptions or remaining operational steps.

## Handling Requests That Could Cause Data Loss

- Stop before execution if the request involves deleting, truncating, dropping, resetting, overwriting, mass-updating, or changing persistent data in place.
- Explain the risk briefly and propose a safer path: backup or snapshot, read-only preview, scoped transaction, idempotent migration, soft delete, archive, or a generated script for operator review.
- If the user explicitly authorizes a destructive operation, still do not execute it automatically. Require a named environment, confirmed scope, verified backup, exact command or script review, and a documented rollback or recovery plan. Prefer handing back the reviewed procedure for an operator to run.

## Completion Criteria

A task is complete only when:

- The requested behavior is addressed with no unnecessary data or system mutation.
- Existing records and unrelated functionality are protected by scope guards or tests.
- The change is reversible or its recovery procedure is documented.
- Focused validation has passed, or unavailable checks and residual risks are clearly reported.
- No credentials or sensitive data were added to files, output, or logs.
