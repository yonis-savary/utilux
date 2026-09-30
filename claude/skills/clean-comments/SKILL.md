---
name: clean-comments
description: Fait le ménage dans les commentaires ajoutés sur la branche courante en appliquant la règle « Comments » des guidelines perso, directement dans le working tree, sans rien commiter
---

Ce skill **modifie des fichiers mais ne commite ni ne pousse jamais**. Il ne touche qu'aux commentaires introduits par la branche : ceux qui existaient déjà sur la base ne sont pas concernés.

**STOPPER TOUT SI UNE DES COMMANDES ÉCHOUE** : le reste du skill repose dessus.

# La règle

La seule référence est la section **Comments** de `/home/yonis/utilux/claude/guidelines.md` (déjà chargée via `CLAUDE.md` ; sinon la lire). Ne pas la reformuler ni l'assouplir ici : chaque décision de l'étape 4 s'y rapporte.

# Étapes

## 1. Branche de base

```bash
gxb
git status --porcelain
```

Déterminer la base exactement comme l'étape 2 du skill `mr-resume`, sans réimplémenter une autre logique. Si l'utilisateur a indiqué une base dans la conversation, la sienne prime. Annoncer la base retenue et continuer.

## 2. Périmètre

```bash
MB=$(git merge-base origin/<base> HEAD)
git diff --name-only $MB
git diff -U0 $MB -- <fichier>
```

Le diff part du merge-base **vers le working tree** : il couvre les commits de la branche et les modifications non commitées. Ignorer les fichiers générés, vendorisés, les lockfiles et les traductions.

## 3. Repérer les commentaires ajoutés

Dans les lignes `+`, relever tout commentaire selon la syntaxe du langage du fichier : docblocks, commentaires de ligne, commentaires de bloc, commentaires dans les templates.

Ouvrir le fichier **dans son état actuel** pour voir chaque commentaire avec le code qu'il accompagne : un commentaire se juge à côté de son code, jamais seul.

Un commentaire qui existait déjà sur la base et n'a été que déplacé ou réindenté n'est pas ajouté : le laisser.

## 4. Décider, commentaire par commentaire

Pour chacun, une seule décision :

- **Supprimer** — il ne relève ni d'une contrainte d'implémentation technique ni d'une règle métier contraignante. C'est le cas par défaut, et celui de tout docblock qui décrit une classe, une méthode, une propriété ou une constante.
- **Réduire** — un docblock qui mêle description et annotation de type utile à l'analyse statique : ne garder que le tag de type. Un tag qui répète le type natif de la signature est supprimé aussi.
- **Raccourcir** — le commentaire est justifié mais dépasse une phrase courte : le ramener à l'essentiel de la contrainte.
- **Garder** — contrainte technique ou règle métier, déjà en une phrase courte. Les `TODO` / `FIXME` / `HACK` avec contexte sont gardés.

Dans le doute, supprimer : c'est ce que dit la règle.

Si un commentaire supprimé compensait un nom ou une structure peu clairs, **ne pas refactoriser** : le signaler dans le compte-rendu, l'utilisateur décidera.

## 5. Appliquer

Faire les suppressions et réécritures. Ne toucher qu'aux commentaires : aucune autre ligne de code ne change.

Laisser le fichier propre : pas de docblock vide, pas de ligne vide orpheline laissée par la suppression, indentation intacte.

**Ne rien commiter, ne rien pousser.**

## 6. Compte-rendu

Afficher :

- la base retenue et le nombre de fichiers examinés,
- le nombre de commentaires supprimés, réduits, raccourcis et gardés,
- pour chaque commentaire **gardé ou raccourci** : `fichier:ligne` et la contrainte qu'il porte, en quelques mots,
- les endroits où un commentaire supprimé masquait un problème de nommage ou de structure,
- la sortie de `git diff --stat`.

Ne pas lister un par un les commentaires supprimés : le diff les montre.
