This directory holds everything needed to test utilux installation process

## Testing installation

Requirements : docker + docker-compose

```bash
make up # docker compose up -d

make exec # docker compose exec vm zsh

# Inside vm service
git clone https://github.com/yonis-savary/utilux ~/utilux && source ~/utilux/install
exit
make exec # Simulate session restart

# ... Test Utilux Features ...

exit

make down # docker compose down -v
```

