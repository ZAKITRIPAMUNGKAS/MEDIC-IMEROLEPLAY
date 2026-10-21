# Repository Rules & Automation Protocols

## Auto-Execute Git Push & Commit
- Whenever requested to commit and/or push:
  1. Stage all changes: `git add -A`
  2. Commit with a meaningful conventional commit message reflecting recent modifications.
  3. Push immediately to `origin main` (or the active branch).
  4. Never ask interactive questions or wait for confirmation.
