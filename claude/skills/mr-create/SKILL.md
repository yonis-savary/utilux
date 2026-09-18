---
name: mr-create
description: Crée la pull request Bitbucket de la branche courante vers sa branche de base, avec une description produite par mr-resume et la fermeture de branche au merge, puis affiche le lien de la PR
---

Ce skill **ne commite pas et ne pousse pas**. Il lit la branche courante telle qu'elle est et crée la pull request correspondante. Si la branche n'est pas déjà sur `origin`, il s'arrête.

**STOPPER TOUT SI UNE DES COMMANDES ÉCHOUE** : le reste du skill repose dessus.

# Étapes

## 1. Description et branche de base

Dérouler intégralement le skill `mr-resume`. Ne pas réimplémenter sa logique : il fournit les deux éléments nécessaires ici.

- La **branche de base** qu'il a retenue, sous la forme `origin/<base>` (son étape 2).
- Le **bloc de description**, dans le format exact de son étape 6.

Si l'utilisateur a indiqué une autre base dans la conversation, la sienne prime.

## 2. Titre de la pull request

Le titre est la ligne `## <CLÉ-TICKET> — <Titre>` du rendu, débarrassée de ses `##` et de ses espaces de bord.

Si aucune clé de ticket n'a été trouvée, cette ligne ne contient que le titre déduit : la reprendre telle quelle, sans inventer de clé.

## 3. La branche doit exister sur origin

```bash
gxb
git ls-remote --exit-code --heads origin "$(gxb)"
```

Si la branche est absente d'`origin`, **s'arrêter** et demander à l'utilisateur de la pousser (`gxp`). Ne jamais pousser à sa place.

## 4. Confirmation

Afficher, **hors du bloc de code** :

- le titre retenu
- `<branche courante> -> <branche de base>`
- la mention que la branche source sera supprimée au merge

puis la description dans son bloc, telle qu'elle partira.

**Attendre un accord explicite.** Ne rien créer avant : une pull request notifie les relecteurs, et la description a été rédigée automatiquement.

## 5. Création

```bash
axbp+ -t "<titre>" -b "<base>" -c -d - <<'EOF'
<description>
EOF
```

- `-b` attend un nom de branche **sans le préfixe `origin/`**
- `-c` ferme la branche source au merge
- `-d -` lit la description sur l'entrée standard, ce qui évite tout problème d'échappement

Le contenu envoyé est celui du bloc de description, **sans les délimiteurs de bloc de code**.

## 6. Rendu

`axbp+` affiche le numéro et l'URL de la pull request créée. Les relayer tels quels, sans les reformuler :

```
Pull request #412 created
https://bitbucket.org/<workspace>/<repo>/pull-requests/412
```

Si la commande échoue, remonter le message d'erreur de l'API sans le réinterpréter.
