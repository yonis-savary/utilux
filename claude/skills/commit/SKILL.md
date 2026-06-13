---
name: commit
description: Décrit le dernier commit en markdown français (court)
---

Génère un résumé du dernier commit en français, sous forme de quelques points de liste (max 10).
Sois concis, focus sur l'essentiel.

Commit : !`git log -1 --format="%B"`
Fichiers modifiés : !`git diff HEAD~1 HEAD --stat`