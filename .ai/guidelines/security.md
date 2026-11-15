# Security

- This is a **single-user application**: the only user is the owner, who can do everything. Access to the app is limited to that user by the admin panel login (`User::canAccessPanel()`), so there is **no per-feature authorization**.
- Do not create Policies, Gates, ability enums, `->authorize()` calls, `Gate::authorize()` checks inside Actions, or authorization tests for new features.
- Never use `$guarded = []` on any model.
- Never unguard any model.
- Cast sensible attributes as `encrypted`.  
  Do not encrypt fields needed for important/recurring matches.  
  For deterministic lookup on an encrypted field, add a **blind index**: a hash column alongside the encrypted one.
- Never write PII to application logs.
- A client-controllable value interpolated into an exception message reaches the log verbatim, so escape its control characters: `addcslashes($value, "\0..\37")`, or a submitted newline forges a log line.
