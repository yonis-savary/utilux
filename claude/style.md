# Code style and structure

How I structure code, at the level of architecture, class design, control flow and design
patterns. Language-agnostic. Derived from reading my own framework code; the patterns below
are the recurring vocabulary, not a description of any particular API.

**Always make use of availables tools/framework-features before implementing**

Companion to `guidelines.md`: that file says *what* to deliver, this one says *what shape*
the result should have. A project with an established structure of its own wins.

## Architecture

- **Concentric layers, one direction.** A kernel (discovery, dependency resolution, error
  handling, lifecycle) knows nothing about the domains; ambient resources (config, cache,
  storage, log, session) sit above it; domain packages (persistence, HTTP, security) sit
  above those; tooling (CLI, test harness) sits outermost and may see everything.
  Dependencies point inward. The only sanctioned exception is that the kernel may know the
  one or two *universal currency types* of the system — the types that cross every layer
  anyway — because dependency resolution has to be aware of them.
- **Folder = namespace = concern.** The directory tree *is* the architecture diagram; if the
  tree doesn't explain the system, the tree is wrong. Nest sub-concerns rather than prefixing
  filenames.
- **One implementation per file, siblings under a shared abstraction.** Interchangeable
  backends live side by side in a leaf folder and never reference each other.
- **Extension by addition, never by modification.** Adding a backend, a command, a rule or a
  route must mean creating one file. If it also means editing a registry, a `switch` or a
  central list, the design is wrong.
- **Capability self-declaration over central dispatch.** An implementation answers *"do you
  handle this?"* itself; the selector loops over candidates and asks. No central map from
  key to class.
- **Registration is derived, not declared.** Wiring comes from scanning what exists —
  classes that extend a base, implement an interface, or sit in a conventionally named
  folder. Nothing has to be listed twice. Derivation is expensive, so it is memoized behind
  an explicit invalidation key (a dependency-lockfile hash, a version), never behind a
  timestamp.
- **Configuration is typed objects, not string maps.** Each subsystem owns a configuration
  class, resolved through one documented fallback chain (explicitly provided → registered →
  built-in default). Config objects hold *ready-made collaborators*, not strings someone must
  later interpret.

## Class design

Four species of class, with different rules. Knowing which one you are writing is most of
the design work.

1. **Services** — behaviour, few or no data. Mutable, fluent, often reachable as an ambient
   instance. They collaborate; they hold no domain data of their own.
2. **Value objects** — data, no behaviour. Immutable, fully initialized by the constructor,
   no accessors (the fields *are* the interface). They are the alphabet the services
   manipulate, especially as nodes of an intermediate representation.
3. **Contracts** — interfaces naming a role, as small as possible, frequently one method.
   An interface exists to allow substitution; one implementation and no substitution
   expected means no interface.
4. **Mixins** — reusable mechanics attached by composition-into-the-class (traits), named
   for the capability they confer. They carry plumbing, never domain rules.

Sizing and shaping:

- **Breadth is free, depth is not.** A class with fifty one-line methods over a single
  canonical implementation is fine — it is vocabulary. A class with five methods that each
  branch four levels deep is not.
- **One canonical implementation per behaviour**; everything else delegates to it. Any
  logic that appears twice is a missing delegation.
- **Inheritance expresses a contract, composition expresses reuse.** Extend to *be
  configured by subclassing*; mix in or delegate for everything else. Never extend to
  inherit convenience.
- **Three-tier contracts on an extension point:** `abstract` for what a subclass *must*
  supply, an overridable method with a sensible default for what it *may*, `final` for the
  skeleton it *must not* touch. Stating all three is how a base class communicates intent.
- **Preserve subtype identity through inheritance.** Inherited fluent and factory methods
  return the *called* type, not the declaring one, so subclasses stay usable without casts.
- **Static means class-level or stateless**: alternate constructors, and the class-level
  declarations a subclass must answer. Anything holding state is an instance.

## Design patterns in use

The recurring set. Reach for these before inventing something.

**Construction**
- *Static factory / named constructor* — the constructor stays dumb (assign, normalize);
  every meaningful way to build the object is a named entry point. No boolean-flag
  constructors.
- *Builder with fluent interface* — configuration steps return the object, one terminal
  operation ends the chain. That asymmetry is what makes the chain readable.
- *Scoped-builder DSL* — for tree-shaped construction, a builder holding a cursor plus a
  callback that opens a nested scope, so structure in the source mirrors structure in the
  result.

**Representation**
- *Intermediate representation + interpreter* — accumulate the request as a tree of small
  value objects, then hand it to a backend-specific renderer. **The single most useful
  structural idea here: never build the output string as you go.** Representation and
  rendering evolve independently, and a second backend costs one class.
- *Composite* — a container implementing the same contract as its children, recursing.
  Applies to validation shapes, route groups, relation trees; error aggregation follows the
  same recursion, keyed by path.
- *Null object and sentinel types* — when `null` is ambiguous (absent vs. present-and-null)
  or would break a contract, introduce an explicit type: a do-nothing implementation of the
  contract, or a distinct "no value" marker.

**Behaviour**
- *Strategy / driver* — a family of interchangeable implementations behind one abstract
  base that also holds their shared helpers.
- *Template method* — the base fixes the algorithm, subclasses fill the holes.
- *Chain of responsibility* — a re-entrant pipeline object passing a continuation to each
  stage, each stage free to short-circuit. Preferred over a hard-coded call order.
- *Step pipeline* — a validated or transformed value walks an ordered list of small
  check/transform steps that were declared elsewhere. A single monolithic `validate()`
  is the thing this replaces.
- *Observer, owned locally* — objects that need to react to each other carry their own
  subscription list rather than routing through a global bus. Coordination stays visible
  in the object that causes it.
- *Adapter at the boundary* — see below.

**Resolution**
- *Reflection-based dependency resolution* with one explicit, ordered rule set (ambient
  instance → configuration object → recursive construction → declared default → fail).
  The order lives in one place and is readable in one screen.
- *Memoization keyed by identity* — expensive derivations cached under a key that encodes
  what they depend on.

## Boundaries

- **Normalize on the way in, coerce on the way out.** The edge converts loose external
  shapes into exact internal types, and internal types into whatever the outside expects.
  Nothing in between re-checks or re-guesses formats. Both directions matter — the outbound
  half is the one people forget.
- **Declare the expected shape, don't check it procedurally.** Input contracts are composed
  declaratively and validated in one call. Scattered presence/format checks in handlers are
  the smell this exists to remove.
- **Validate once, then trust.** Inside the boundary, internal calls are assumed correct;
  no defensive re-validation, no fallbacks for states that cannot occur.

## Control flow

- **Guard clauses only; the happy path stays at the left margin.** Reject early, return
  early. No `else` after a `return` or `throw`.
- **Nesting budget: two.** A third level means extracting a function or inverting a
  condition. Loop bodies start with `continue` guards rather than wrapping their work in
  an `if`.
- **Bind and test in the same guard** when the value is only needed on the success path.
- **Table-driven or expression-level dispatch** over branch ladders: a lookup structure, or
  a total expression mapping input to output. A `switch` survives only for genuinely
  positional, fall-through cases.
- **Recursion for recursive data.** Tree walks are small recursive helpers with the
  accumulated path passed down as a parameter — not iterative stacks, not global state.
- **Functional pipelines for transformation, plain loops for accumulation.** Chains of
  map/filter/reduce when data flows through unchanged shapes; an explicit loop when you are
  building something up or causing effects. Don't force effects into a pipeline.
- **Lazy defaults at the top of the body** (`x = x ?? resolve()`), so signatures stay
  optional and bodies stay unconditional.
- **In-place mutation through a reference is a power tool.** Legitimate for hydrating a
  nested structure through a cursor, or handing a caller a mutable slot in a store. Anywhere
  else it is a bug waiting to be found — every use should be explainable in one sentence.

## Failure

- **Two audiences, two mechanisms.** A misused API or missing required configuration throws
  at the point of detection. An invalid *input* returns a typed result the caller can act
  on. Never an exception for expected user behaviour, never a status code for a programmer
  error.
- **Carry the outcome in the exception** when a deep layer must abort with a user-facing
  result: throw something that holds the finished response, and let the boundary unwrap it.
  This is what keeps error codes from being threaded back up through every frame.
- **Diagnostics, not labels.** A failure message names the type, the field, the actual value
  and the expected one. Messages that could be pasted into a bug report without extra
  context.
- **One outermost safety net** that logs and degrades, with its own inner fallback for when
  logging itself fails. Everything beneath it is free to fail loudly.
- **Templated messages with a context map**, never concatenation, for anything logged or
  surfaced — interpolation is the formatter's job, and structured context stays queryable.

## Declarative descriptors

The strongest leverage in the codebase, worth reaching for deliberately: **describe a thing
once as data, then project that description into every consumer.** One field description
drives the storage schema, the input validation rule, the generated code and the public API
documentation. The alternative — four hand-maintained copies that silently drift — is the
default outcome and must be actively avoided.

Corollary: descriptors carry an open metadata map for consumer-specific extras, so adding a
consumer never changes the descriptor's own shape.

## Naming

- Verb prefixes form a small closed vocabulary with fixed meanings — one for cheap
  accessors, one for predicates, one for builder steps, one for alternate constructors, one
  for conversions. Consistency across the codebase matters more than which words you pick.
- Plural names mean collections; two names beat one name plus a "multiple" flag.
- Interfaces are named for the role they confer, mixins for the capability they add.
- Names are prose. Length is cheap, re-reading is not.

## Comments

Default to none — naming and structure carry the *what*. Roughly one comment per file, for:
a non-obvious optimisation and why it is safe; a deliberate indirection and the reason it
exists; a case intentionally left unhandled; a `TODO` with its concrete follow-up. Public
entry points get a docblock explaining what the type is *for* and how it is meant to be
entered.

## Deliberately absent

Knowing what I *don't* do is half the style: no wiring configuration files and no wiring
annotations (discovery and reflection cover it); no accessor boilerplate on value objects;
no interface without a second implementation in sight; no exception used for expected
control flow; no class closed to extension unless there is a reason; no output built by
string concatenation where an intermediate representation would do.
