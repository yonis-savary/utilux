# Scripts

Notes on the utilux scripts that need more than their `--help`.

## Mount a remote SSH directory

```bash
# Install sshfs first
sudo apt-get install sshfs

# Add a ssh key
utilux-ssh a my-vps.com
ssh-copy-id -i ~/.ssh/my-vps.com.pub user@my-vps.com
sudo mkdir -p /mnt/my-vps.com

# test sshfs connection
sshfs -o IdentityFile=~/.ssh/id_ed25519 ton_user@ip:/home/dist-user /mnt/my-vps.com
# unmount test directory
fusermount -u /mnt/my-vps.com

# Allow other file (useful if a service like docker/php has to read/write from dist directory)
# Do it only once
echo "user_allow_other" | sudo tee -a /etc/fuse.conf

# In /etc/fstab
# Warning: please verify your uid/gid values
# uid=$(id -u)
# gid=$(id -g)
user@my-vps.com:/home/dist-user /mnt/my-vps.com fuse.sshfs _netdev,IdentityFile=/home/local-user/.ssh/my-vps.com,users,idmap=user,allow_other,default_permissions,uid=1000,gid=1000 0 0

# Check fstab syntax file
sudo findmnt --verify

# Manually mount without rebooting (unmount with fusermount -u /mnt/my-vps.com)
# Then auto-mounted on reboot when configured through /etc/fstab
sudo mount /mnt/my-vps.com
```

## Password protected vault

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

## Skeleton directory

`utilux-skeln` gathers scattered files and directories in one store and leaves symlinks in their place,
so a single directory holds everything worth syncing or backing up (see `utilux-launch-backup`).

```bash
utilux-skeln mount ~/Documents            # move ~/Documents into the store, leave a symlink behind
utilux-skeln mount /etc/nginx --name=nginx  # custom identifier instead of a random hex one
utilux-skeln ls                           # list registered entries (installed / missing)
utilux-skeln umount ~/Documents           # move it back (by path or identifier)
utilux-skeln install                      # recreate missing symlinks, e.g. on a new machine
utilux-skeln zip [archive.zip] [-p]       # archive the store and the registry (-p asks for a password)
utilux-skeln unzip <archive.zip>          # set up an empty skeln from an archive, then install it
utilux-skeln unzip <archive.zip> --patch /home/yonis=/home/devuser  # same, rewriting the registered paths
```

The store always lives in `$UTILUX_APPDATA_PATH/skeln`. `--root` (default `/`) allows testing without touching the
real filesystem: `utilux-skeln --root=/tmp/testenv mount /etc/nginx`.

```
~/.local/share/utilux/skeln/
├── registry              # <id>:<original path>, one entry per line
└── store/
    ├── e18864d7b7674e60/ # contents of ~/Documents
    └── nginx/            # contents of /etc/nginx

~/Documents -> ~/.local/share/utilux/skeln/store/e18864d7b7674e60
```

- `install` only creates a symlink where nothing exists yet (or an empty directory), anything else is reported and skipped.
- `zip` and `unzip` need `7z` (`p7zip-full`). With `-p`, the archive is encrypted with AES-256: `7z` opens it,
  the `unzip` command does not. The password is asked on the terminal, `unzip` asks it only for an encrypted archive.
- `unzip` refuses to run when the store or the registry already holds something. The registry keeps absolute
  paths, so the links are created at the same paths as on the zipped machine, unless `--patch` rewrites them.
- `--patch OLD=NEW` (repeatable) replaces the `OLD` directory prefix with `NEW` in the registry before `install`.
  It matches whole directories: `/home/yon` matches `/home/yon/sub` but not `/home/yonis`, and when several patches
  match, the longest prefix wins. A malformed patch, or a patch matching no registered path, stops the command and
  leaves skeln empty.
- `utilux-skeln` refuses to run as root and only supports Linux.
