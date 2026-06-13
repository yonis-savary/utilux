---
name: code-issue
description: Va chercher un ou plusieurs tickets Jira par ligne de commande, créé la branche associé au premier ticket et propose un plan de développement
---

**ÉTAPE 1 OBLIGATOIRE** : Si aucune clé de ticket n'est fournie en argument du skill, poser la question à l'utilisateur et ATTENDRE sa réponse avant de continuer. Ne jamais exécuter la commande sans avoir une clé de ticket réelle.

Une fois la/les clés obtenue (ex: `PRJCT-1234`), exécuter (par ticket) via Bash en remplaçant `TICKET_KEY` par la vraie valeur :

```
bash ~/utilux/modules/scripts/jira/jx fetch TICKET_KEY
```

STOPPER TOUT SI UNE DES COMMANDES ECHOUE, LE RESTE DU SKILL REPOSE DESSUS

# Étapes

## 1. Branche

Propose d'abord un nom pour le premier ticket sous le format `<feat|fix|epic>/<issue_key>/<résumé_en_quelques_mots_clés>`

Avec comme règle pour le préfixe
- Bug => `fix`
- Epic => `epic`
- Autres => `feat`

Exemple
1. [Bug] PRJCT-5024 Rajouter un connecteur GitHub pour commit => `fix/PRJCT-5024/rajouter-connecteur-github-pour-commit`
2. [Chore] EXPRMT-284 Upgrade de PHP 7 vers PHP 8 => `feat/EXPRMT-284/upgrade-php-7-vers-8`

**attendre validation par l'utilisateur, celui-ci peux aussi choisir manuellement le nom de la branche**

Une fois un nom choisi, créer la branche avec `git checkout -b`

## 2. Plan de Développement

Ensuite, le but est de proposer un plan de développement pour le ticket, avec la logique métier derrière, les fichiers édités, dans quels ordre, et les points important à garder en tête ou les sujets hors-scope qui se retrouvent touchés par ce ticket

**demander à l'utilisateur si celui-ci possèdes des informations utiles au développement (plan technique déjà pensé, contraintes techniques/métiers, information importantes quelconque)**

### Ordre de développement privilégié (pas obligatoire)

Voici le format standard attendu pour un plan de développement (Dans le cas d'un développement très court type 2-3 fichiers touchés pour un fix rapide, celui-ci peut être adapté/raccourci)

- **Migrations** : oui/non + contenu si oui
- **Fichiers à modifier** : liste ordonnée avec rôle de chaque fichier
- **Points d'attention** : règles métier, contraintes, effets de bord
- **Hors scope** : ce qui est volontairement laissé de côté


**attendre validation par l'utilisateur du plan de développement**

Une fois le plan validé, écrire dans les différents fichiers les modifications proposés (en ignorant les modifications que l'utilisateur aura refusé)

## 3. Finalisation

Proposer un commentaire de commit succin sous la forme 

`<feat|fix|epic>: <commentaire_en_quelques_mots_(anglais)>, <issue_key>`

Ne pas attendre de validation, c'est la responsabilité de l'utilisateur de commit son code