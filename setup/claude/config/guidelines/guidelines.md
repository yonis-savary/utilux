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

**No comment is the rule.** Code must read on its own in 99% of cases: names and
structure carry the *what*, the *how* and usually the *why*. A comment exists **only
and exclusively** for one of two reasons:

1. **A technical implementation constraint** the code cannot express — a workaround for
   a library or platform bug, a non-obvious ordering or side effect, a magic number
   imposed from outside, a performance trick and why it is safe, the intent of a regex.
2. **A business rule that imposes a constraint** — a regulation, a legal or accounting
   norm, a contractual requirement: something that would look arbitrary or wrong to a
   reader who doesn't know the domain rule behind it.

If a comment does neither, it does not get written. When unsure, don't write it.

- **No docblocks by default.** Do not put a comment above a class, method, property or
  constant to describe what it is or does — that is the name's job. No summary lines,
  no "This class handles…", no `@param` / `@return` that repeat the signature. The only
  acceptable docblock content is a type annotation the language cannot express natively
  (e.g. generic or array shapes) that static analysis needs — and only that tag.
- **Technical debt:** `TODO` / `FIXME` / `HACK` **with context** — a name or ticket
  number — so it is actionable. This is the only place a ticket reference belongs.
- **Never:** restate or paraphrase code; describe a class or method; tag a comment with
  the ticket or PR that introduced it; leave commented-out code; add section markers,
  banners or divider lines; narrate a refactor ("simplified from previous version" is a
  commit message).
- **One line (One short sentence at most).** A justified comment is short and sits right next to the
  line it constrains. Keep it in sync — an outdated comment is worse than none.
- **Before handing over a diff, reread every comment you added** and delete each one
  that is not a technical constraint or a business rule.

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
