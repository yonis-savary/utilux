This directory holds everything needed to test utilux installation process

## Testing installation

Requirements : docker + docker-compose

```bash
make up # copy the working tree into .build/utilux, then docker compose up -d --build

make exec # docker compose exec vm zsh

# Inside vm service
source ~/utilux/install
exit
make exec # Simulate session restart

# ... Test Utilux Features ...

exit

make down # docker compose down -v
```

`make up` stands for the `git clone`: `~/utilux` in the container is the current working tree, uncommitted changes
included, as `git add -A` would see it (tracked and new files, never the ignored ones) plus `.git`.

`shared/` is mounted in the container as `~/shared`, to bring files in (a `utilux-skeln zip` archive, for instance).
Its content is ignored by git.

## Testing utilux-skeln

Requirements : pytest

```bash
make skeln # pytest skeln/ -v
```

Tests run in temporary directories, the real filesystem is never touched.
