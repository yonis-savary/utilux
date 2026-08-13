# Personal guidelines

These apply to every project, whatever the stack. Project-level `CLAUDE.md` files
add stack-specific rules and take precedence where they conflict.

## Communication

- If information is missing or ambiguous, ask before writing code. Never rely on an
  implicit assumption — decisions must rest on explicit information.
- When several approaches are viable, present the options with their trade-offs and a
  recommendation, then wait. Don't silently pick one and build it.
- Lead with the outcome. The first sentence should answer "what happened" or "what did
  you find"; supporting detail comes after.
- Be concise, but readable over terse. Drop details that don't change what I'd do next
  rather than compressing prose into fragments, abbreviations or arrow chains.
- Report faithfully: if tests fail, say so with the output; if a step was skipped, say
  that; when something is done and verified, state it plainly without hedging.

## Scope

- Deliver what was asked, at the scope intended. Don't quietly narrow, widen or
  transform it. If you think the ask is mistaken, say so in a sentence and continue.
- Don't add features, refactors or abstractions beyond the task. A bug fix doesn't need
  surrounding cleanup. Don't design for hypothetical future requirements.
- Don't add error handling, fallbacks or validation for cases that cannot happen. Trust
  internal code and framework guarantees; validate at system boundaries only.
- Finish the whole task. Only report completion when it is actually complete; if
  something is genuinely blocked, do the rest and say plainly what is missing and why.

## Design principles

- **Fail fast** — validate inputs early, return or throw as soon as something is wrong.
- **Separation of concerns** — business logic, presentation, data access and utilities
  each live in their own layer.
- **Consistency over cleverness** — follow the existing patterns, naming and structure
  of the codebase, even when another approach looks smarter.
- **Dependencies** — do not introduce a new external dependency unless strictly
  necessary. Explain the proposal before using it.

## Comments

Default to **no comment**. The code is the comment — clear names and structure carry
the *what*. Write a comment only when it explains something the code cannot, and
delete it the moment it stops earning its place.

- **Comment the why, not the what.** Explain intent, reasoning and decisions, not
  syntax. Never use a comment to compensate for code you could fix — rename the
  variable or extract the method instead.
- **What earns a comment:** workarounds, magic numbers, regex intent, business rules,
  external constraints; edge cases and what is intentionally *not* handled; danger
  warnings (side effects, order dependencies); contracts (units, formats, encodings).
- **Public interfaces:** document them in the language's standard format so tooling can
  use them. A docblock that merely restates the signature is noise.
- **Technical debt:** mark it `TODO` / `FIXME` / `HACK` **with context** — a name or
  ticket number — so it is actionable. This is the only place a ticket reference
  belongs in code.
- **Never:** restate code; tag a comment with the ticket or PR that introduced it;
  leave commented-out code; add section markers, banners or divider lines; narrate a
  refactor ("simplified from previous version" is a commit message).
- **Keep them honest.** An outdated comment is worse than none. Keep comments in sync,
  close to what they describe, concise, and consistent in style.

## Shared knowledge

Durable project knowledge belongs **in the repository**, where the team and their
agents can see it: feature docs, code comments, PR descriptions. Do not record
shareable knowledge in agent-local memory — it is invisible to teammates. When you
learn something worth keeping (a flow, a decision, a gotcha), write it to the repo.

## Git

- Never commit or push unless I ask. Never create a branch without being asked which
  one, and never work directly on a protected branch.
- One concern per commit. Write the message about *why*, not a restatement of the diff.
- Interactive git flags (`-i`) don't work here; don't reach for them.
