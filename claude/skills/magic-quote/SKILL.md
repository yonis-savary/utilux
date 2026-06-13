---
name: magic-quote
description: Va chercher un ticket Jira par ligne de commande dans le but d'aider à estimer le temps de développement
---

**ÉTAPE 1 OBLIGATOIRE** : Si la clé du ticket n'est pas fournie en argument du skill, poser la question à l'utilisateur et ATTENDRE sa réponse avant de continuer. Ne jamais exécuter la commande sans avoir une clé de ticket réelle.

Une fois la clé obtenue (ex: `PRJCT-1234`), exécuter via Bash en remplaçant `TICKET_KEY` par la vraie valeur :

```
bash ~/utilux/modules/scripts/jira/jx fetch TICKET_KEY
```

STOPPER TOUT SI CETTE COMMANDE ECHOUE, LE RESTE DU SKILL REPOSE DESSUS
NE CHERCHER QUE LES INFOS SUR LE TICKET DEMANDÉ NE PAS ALLER CHERCHER LES ENFANTS/SOUS-TACHES

## Estimation du temps

Le but est d'estimer la complexité de l'implémentation nécessaire en réponse au ticket indiqué par l'utilisateur

Aller chercher dans le code les différents fichiers concernés par la feature, voir si les user-story/cas-d'utilisations ne sont pas incompatible avec la codebase actuelle

Donner une estimation réaliste du temps de développement, sachant que l'agent Claude IA nous fait économiser une bonne moitié de temps de dev

## Format

Pour le ticket indiqué, donner un résumé tel que:
- Nom du Ticket
- Temps estimé en heures
- Point(s) Important(s) si gros refacto

Puis un total du temps estimé en heures
