---
name: mr-resolve
description: Récupère les commentaires de la pull request de la branche courante, applique les corrections pertinentes dans le working tree sans rien commiter, puis marque les fils traités comme résolus sur Bitbucket
---

Ce skill **modifie des fichiers mais ne commite ni ne pousse jamais**. Il n'écrit sur Bitbucket que pour fermer des fils, et seulement après accord explicite de l'utilisateur.

**STOPPER TOUT SI UNE DES COMMANDES ÉCHOUE** : le reste du skill repose dessus.

# Étapes

## 1. Retrouver la pull request

```bash
gxb
axbp | jq -r --arg b "$(gxb)" 'map(select(.source.branch.name == $b)) | .[] | "\(.id)\t\(.title)"'
```

- Aucune pull request pour cette branche → s'arrêter et le dire, en proposant `mr-create`.
- Plusieurs → demander laquelle avant de continuer.

## 2. Récupérer les commentaires à traiter

```bash
axbpc <PR-ID> -o
```

`-o` écarte déjà les commentaires supprimés et les fils déjà résolus. Si la liste est vide, s'arrêter : il n'y a rien à faire.

## 3. Trier, sans encore rien corriger

Classer chaque commentaire dans une de ces trois catégories :

- **Machinerie** — marqueurs HTML de bot, récapitulatifs automatiques, « Actionable comments posted », invitations à lancer une commande de bot. Ignorer.
- **Demande actionnable** — un problème précis dans le code, généralement avec un fichier et une ligne. Candidat à correction.
- **Discussion ou décision** — une réponse, un arbitrage déjà tranché, une question ouverte. Ne pas corriger : le remonter à l'utilisateur.

`.inline.path` et `.inline.to` donnent le fichier et la ligne, `.user.display_name` l'auteur. Attention : **un bot produit aussi des demandes parfaitement valables**. C'est le contenu qui décide de la catégorie, jamais l'auteur.

## 4. Vérifier la pertinence

Pour chaque demande actionnable, ouvrir le fichier concerné **dans son état actuel** et vérifier que le problème existe encore : un commentaire peut porter sur du code modifié depuis.

Écarter, en disant pourquoi :

- ce qui est déjà corrigé sur la branche,
- ce qui contredit une décision explicite prise dans un autre commentaire ou dans la conversation,
- ce qui demande un arbitrage d'architecture non tranché.

Dans le doute, écarter et remonter : une correction non désirée coûte plus cher qu'un commentaire laissé ouvert.

## 5. Corriger

Appliquer les corrections retenues, une par commentaire traité.

Ne corriger **que** ce que le commentaire demande. Pas de nettoyage alentour, pas de refactorisation d'opportunité.

**Ne rien commiter, ne rien pousser.**

## 6. Compte-rendu, puis accord

Afficher :

- la pull request et le nombre de commentaires examinés,
- pour chaque commentaire : sa catégorie, `fichier:ligne`, et ce qui a été fait ou la raison de l'écart,
- la sortie de `git diff --stat`.

Puis **attendre un accord explicite** avant l'étape 7 : fermer un fil est visible par les relecteurs.

## 7. Marquer les fils résolus

```bash
axbpcr <PR-ID> <COMMENT-ID> [COMMENT-ID...]
```

Uniquement les commentaires réellement corrigés à l'étape 5 — jamais ceux écartés, jamais ceux relevant de la discussion.

Seul un **début de fil** peut être résolu. Si le commentaire traité est une réponse (champ `.parent` présent), passer l'id de la racine du fil.

En cas d'échec sur un id, la commande poursuit les suivants et sort en erreur : indiquer lesquels n'ont pas pu être fermés.
