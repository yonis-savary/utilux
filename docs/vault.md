# Password protected vault

`utilux-vault` shares a directory with a personal server without leaving it reachable from an open session:
nothing is written to `/etc/fstab` and no SSH key is involved, the server password is asked on every mount.

```bash
utilux-vault mount   # mounts the remote directory in ~/.local/share/utilux/vault
utilux-vault umount  # unmounts it
```

- Configuration comes from `utilux-config` (see the Vault section of `.shellrc.custom.example`), every variable is optional:

  | Variable                   | Default                         |
  |----------------------------|---------------------------------|
  | `UTILUX_VAULT_HOST`        | asked on mount                  |
  | `UTILUX_VAULT_USER`        | `$USER`                         |
  | `UTILUX_VAULT_REMOTE_PATH` | `/home/$UTILUX_VAULT_USER/vault` (must exist) |

- The server must accept password authentication (`PasswordAuthentication yes` in `sshd_config`).
  Public key authentication is disabled on the client side.
- `sshfs` runs inside the `utilux-vault` user scope (`systemd-run --user --scope`), so it is stopped, and the
  vault unmounted, when the user manager stops, that is when your last session (KDE, TTY or SSH) closes.
  This relies on lingering being disabled (`loginctl show-user $USER -p Linger` must print `Linger=no`).
- While mounted, the vault is readable by anyone using your session.
