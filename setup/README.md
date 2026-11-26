# Setup modules

Every directory here is a module holding one piece of my setup. `utilux-setup` and `utilux-setup-save` find the
modules by themselves, a new one only has to follow the layout below.

```
setup/<module>/
├── README.md        # its "# Title" is the label shown in the menus
├── requires         # a command that must exist for the module to apply (e.g. code, plasmashell)
├── apply-config     # applies the template on the machine
└── export-config    # optional, replaces the template with the machine configuration
```

| Command | Menu content |
|---|---|
| `utilux-setup` | The modules with an `apply-config`, after the software menu |
| `utilux-setup-save` | The modules with an `export-config`, then offers to commit and push the template changes |

Nothing is checked by default in either menu. Modules linking their file into the home folder (WezTerm, Claude Code)
have no `export-config`: their changes already land in the repository.
