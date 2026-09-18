---
name: mr-resume
description: Résume ce qui a été développé sur la branche courante (contexte Jira + diff + tests) au format d'une description de merge request, prête à coller
---

Ce skill **ne modifie jamais le dépôt** : pas de checkout, pas d'édition, pas de commit, pas de push. Il lit la branche courante telle qu'elle est.

STOPPER TOUT SI UNE DES COMMANDES ECHOUE, LE RESTE DU SKILL REPOSE DESSUS

# Étapes

## 1. Branche courante

```
gxb
git status --porcelain
```

Si le working tree n'est pas propre, l'annoncer : le résumé portera sur les commits uniquement, les modifications non commitées seront ignorées. Continuer sans attendre.

## 2. Branche de base

Déterminer la base du diff parmi les branches distantes candidates (`develop`, `main`, `master`, et toute branche d'intégration visible dans le dépôt) :

```
git rev-parse --verify origin/<candidate>
git rev-list --count $(git merge-base origin/<candidate> HEAD)..HEAD
```

Ignorer les candidats inexistants ; la base retenue est celle qui donne le **plus petit** nombre de commits d'écart.

Annoncer la base retenue et le nombre de commits, puis continuer. L'utilisateur corrigera s'il veut une autre base.

Toute la suite utilise `BASE=origin/<base>` et la syntaxe **trois points** (`$BASE...HEAD`), qui isole le travail de la branche sans montrer ce qui a été mergé dans la base entre-temps.

## 3. Contexte ticket (non bloquant)

Extraire une clé de ticket du nom de branche (motif `[A-Z][A-Z0-9]+-[0-9]+`, ex : `feat/PRJCT-1234/...`).

**Le contexte de la conversation est la source prioritaire.** Dans la plupart des cas, le contenu du ou des tickets a déjà été fourni par l'utilisateur : le réutiliser tel quel et **ne pas appeler Jira**.

N'aller chercher le ticket que si aucune information à son sujet n'est disponible dans la conversation :

```
axji TICKET_KEY
```

Le ticket fournit le **titre** et la **problématique** du rendu. Si aucune clé n'est présente ou si la commande échoue, continuer : le titre est déduit des commits, la problématique du diff, et le manque de contexte ticket est signalé hors du bloc de rendu. C'est la seule étape autorisée à échouer sans tout arrêter.

## 4. Lecture du travail

```
git log --no-merges --format='%h %s' $BASE..HEAD
git diff --stat $BASE...HEAD
git diff $BASE...HEAD
```

Si le diff est trop volumineux, le lire fichier par fichier (`git diff $BASE...HEAD -- <fichier>`) plutôt que de le tronquer.

Pour chaque fichier touché, la question à laquelle il faut savoir répondre est **« à quoi sert ce fichier dans ce changement »**, pas « quelles lignes ont bougé ». Ouvrir le fichier entier quand le diff seul ne le dit pas.

## 5. Tests

Séparer les fichiers de test des autres dans le diff (conventions du dépôt : `tests/`, `*Test.php`, `*.test.ts`, `*_test.go`…).

- Aucun test ajouté sur la branche → le dire explicitement, ne pas supprimer la section.

## 6. Rendu

Répondre en français, dans un **bloc de code markdown** copiable tel quel dans la merge request, au format exact ci-dessous. Rien avant, rien après, à l'exception des remarques de déroulement (base retenue, ticket absent, tests non lancés) qui restent **hors du bloc**.

````
## tldr;

<Une seule phrase, 400 caractères max, qui résume le travail>

## <CLE-TICKET> - <Titre>

<Problématique ou besoin, 1 à 3 phrases>

## Changements

- `chemin/fichier.ext` — <rôle du fichier dans le changement, quelques mots>
- ...

## Tests

<Tests ajoutés + résultat de l'exécution>

## Résultat QA Navigateur

<Tableau des tests & des résultats de la QA Navigateur>

## Points d'attention

- <Faille potentielle ou point de vigilance>
````

Règles de rédaction :

- **`tldr;` : une seule phrase**, 400 caractères max, sans liste ni sous-phrases. Mots simples et familiers du domaine, pas de jargon technique ni de nom de fichier ou de classe. Elle doit suffire à un relecteur pressé pour savoir ce que fait la branche.
- **Le plus succinct possible.** Une ligne par fichier, l'idée générale en quelques mots. Pas de détail d'implémentation, pas de nom de méthode, pas de justification.
- Décrire une **utilité ou une responsabilité**, pas une opération : « validation du format d'import » plutôt que « ajout de la méthode `checkFormat()` ».
- Regrouper sur une seule ligne les fichiers d'un même lot mécanique (migrations, traductions, fichiers générés, renommage en masse).
- Ne rien inventer : ce qui n'est pas lisible dans le diff, le ticket ou la sortie des tests n'apparaît pas.
- La section **`Points d'attention` est optionnelle** : elle n'existe que s'il y a une faille de sécurité potentielle, un risque de régression, une rupture de compatibilité, une migration ou une action manuelle à prévoir au déploiement. Sinon, l'omettre entièrement — ne jamais écrire « RAS ».
- Si le ticket n'a pas été récupéré, le titre reprend le nom de la branche, sans clé inventée.
- N'afficher la section "Résultat QA Navigateur" qu'uniquement si une QA a effectivement été faite