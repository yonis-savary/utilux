SHELL := /bin/bash

context:
	rm -rf .build && mkdir -p .build/utilux
	cd .. && git ls-files -z --cached --others --exclude-standard \
		| while IFS= read -r -d '' file; do [ -e "$$file" ] || [ -L "$$file" ] && printf '%s\0' "$$file"; done \
		| tar --null -T - -cf - | tar -xf - -C test/.build/utilux
	cp -a ../.git .build/utilux/.git
exec:
	docker compose exec vm zsh
up: context
	docker compose up -d --build
down:
	docker compose down -v
skeln:
	pytest skeln/ -v
