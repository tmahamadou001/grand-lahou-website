# Mettre le site en ligne

Ce document décrit la mise en production du site de la Mairie de Grand-Lahou :
choix de l'hébergement, migration depuis le poste de développement,
configuration de sécurité, puis entretien courant.

Il s'adresse à la personne technique qui réalise la mise en ligne, pas à
l'agent municipal — pour la rédaction quotidienne, voir
[guide-agent-municipal.md](guide-agent-municipal.md).

---

## 1. Ce que contient le dépôt, et ce qu'il ne contient pas

**Déployer ce site n'est pas un `git clone`.** Le dépôt ne porte que le code
écrit pour la mairie. Trois éléments vivent ailleurs et doivent être
transportés séparément :

| Élément | Emplacement en développement | Dans le dépôt |
|---|---|---|
| Thème `grand-lahou` | `wp-content/themes/grand-lahou/` | ✅ |
| Socle métier (types de contenu, champs, sécurité) | `wp-content/mu-plugins/` | ✅ |
| Cœur de WordPress | volume Docker `wp_core` | ❌ |
| Images et documents envoyés | volume Docker `wp_uploads` | ❌ |
| Base de données (tout le contenu rédigé) | volume Docker `db_data` | ❌ |
| Extensions tierces | volume Docker `wp_core` | ❌ |

C'est un choix délibéré : le dépôt reste lisible et ne contient aucun binaire.
Mais il impose de ne pas oublier les images, erreur classique qui donne un site
en ligne avec des cadres vides partout.

---

## 2. Le nom de domaine

Le `.ci` s'obtient auprès d'un bureau d'enregistrement accrédité par le
**NIC.CI**. Le suffixe `.gouv.ci` est réservé aux entités gouvernementales et
suppose une autorisation : à vérifier auprès du NIC.CI si la mairie le
souhaite plutôt qu'un `.ci` simple.

Trois précautions qui évitent la disparition du site dans deux ans :

- Enregistrer le domaine **au nom de la mairie**, jamais au nom d'une personne.
- Utiliser une adresse de contact **institutionnelle** (`contact@…`), pas la
  boîte personnelle du prestataire : c'est à cette adresse que partent les
  avis d'expiration.
- Noter la date d'expiration dans un endroit partagé et activer le
  renouvellement automatique.

---

## 3. Choisir l'hébergement

Les critères qui comptent réellement pour une collectivité, dans l'ordre :

1. **Facturation en bonne et due forme**, compatible avec une dépense publique.
2. **Renouvellement indépendant d'une carte bancaire personnelle.** C'est la
   première cause de mort des sites de collectivités.
3. **Support en français.**
4. **Sauvegardes automatiques incluses** côté hébergeur, en plus des nôtres.
5. **PHP 8.1 minimum** (le site tourne en 8.3) et accès SSH ou WP-CLI, qui
   simplifient beaucoup la migration.

La latence depuis Abidjan est un critère secondaire : un centre de données
européen répond correctement, et Cloudflare (section 6) sert de toute façon
les visiteurs ivoiriens depuis son cache.

**Suffisant et recommandé :** un mutualisé sérieux à 70–150 €/an, type
o2switch, Infomaniak, OVH ou Hostinger. **Surdimensionné :** les offres
WordPress infogérées (Kinsta, WP Engine), à plusieurs centaines d'euros par an
et facturées en devise étrangère.

Ces ordres de prix sont à revérifier, ils évoluent.

---

## 4. La migration, pas à pas

### 4.1 Exporter depuis le poste de développement

Le dossier `tools/` est monté dans le conteneur : l'export atterrit
directement dans le dépôt.

```bash
docker compose run --rm cli wp db export /tools/grand-lahou.sql
```

Puis les images, qui sont dans un volume Docker et non sur le disque :

```bash
docker compose cp wordpress:/var/www/html/wp-content/uploads ./uploads-export
```

> ⚠️ `grand-lahou.sql` contient les adresses des abonnés à la newsletter. Ne le
> committez pas et supprimez-le une fois la migration terminée.

### 4.2 Installer WordPress chez l'hébergeur

Tous les mutualisés proposent une installation en un clic. Choisissez la même
version majeure qu'en développement, et **le français comme langue**.

### 4.3 Déposer le code

Par FTP, SFTP ou SSH, copiez :

- `wp-content/themes/grand-lahou/` → `wp-content/themes/grand-lahou/`
- `wp-content/mu-plugins/` → `wp-content/mu-plugins/`
- le contenu de `uploads-export/` → `wp-content/uploads/`

Vérifiez que `wp-content/uploads` est accessible en écriture par le serveur web.

### 4.4 Importer la base et corriger les adresses

Importez `grand-lahou.sql` (phpMyAdmin ou `wp db import`), puis remplacez les
URL. **Passez impérativement par WP-CLI** et non par un chercher/remplacer SQL :
certaines URL sont enfouies dans des données sérialisées, qu'un remplacement
brut corrompt silencieusement — les réglages du thème sont les premiers touchés.

```bash
wp search-replace 'http://localhost:8080' 'https://www.mairie-grandlahou.ci' --all-tables --precise
```

Si l'hébergeur ne fournit pas WP-CLI, utilisez l'outil
[Search Replace DB](https://interconnectit.com/search-replace-db/), qui gère
la sérialisation, puis **supprimez-le du serveur immédiatement après** : laissé
en place, il donne un accès complet à la base.

Reconstruisez ensuite les permaliens, sans quoi les pages « Démarches » et
« Services » renverront des erreurs 404 :

```bash
wp rewrite flush --hard
```

### 4.5 Reprendre la main sur les comptes

L'import a ramené le compte de développement (`admin` / `admin`). **Avant que
le site soit joignable publiquement :**

1. Créer un compte **Administrateur nominatif** (`p.kouassi`, pas `admin`) avec
   un mot de passe long et unique.
2. Se reconnecter avec ce compte.
3. Supprimer le compte `admin`, en réattribuant ses contenus au nouveau compte.
4. Créer un compte **Éditeur** pour l'agent municipal.

### 4.6 Passer en mode production

Dans `wp-config.php` :

```php
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'DISALLOW_FILE_EDIT', true );
```

Supprimez ensuite `wp-content/debug.log` s'il existe : il est lisible depuis le
web et peut contenir des chemins serveur et des requêtes.

Renouvelez enfin les clés de sécurité (elles ont été générées pour
l'installation locale) : copiez de nouvelles valeurs depuis
<https://api.wordpress.org/secret-key/1.1/salt/> dans `wp-config.php`. Cela
déconnecte toutes les sessions ouvertes, ce qui est précisément le but.

---

## 5. Ce que le code assure déjà

Le module `wp-content/mu-plugins/grand-lahou-core/security.php` traite trois
sujets, sans dépendre d'une extension :

- **L'identité des comptes n'est plus publique.** `/wp-json/wp/v2/users`,
  `/?author=1`, `/author/<nom>/` et le plan de site renvoient désormais une
  erreur 404 aux visiteurs anonymes, et les messages d'erreur de connexion ne
  distinguent plus « identifiant inconnu » de « mot de passe incorrect ».
- **Les entêtes de sécurité** sont envoyés à chaque réponse
  (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`,
  `Permissions-Policy`, et `Strict-Transport-Security` dès que le site est en
  HTTPS).
- **Les formulaires publics sont plafonnés** : cinq messages de contact et
  trois inscriptions à la newsletter par heure et par adresse IP.

Deux réglages restent à faire à la main une fois en ligne.

### 5.1 Masquer la version de PHP et d'Apache

Le code retire `X-Powered-By`, mais la bannière du serveur dépend de
l'hébergeur. Si vous avez la main sur la configuration :

```apache
ServerTokens Prod
```

et `expose_php = Off` dans le `php.ini`. Sur un mutualisé sans accès, ce point
est mineur — Cloudflare masque de toute façon la bannière d'origine.

### 5.2 Content-Security-Policy

Aucune CSP n'est posée par le code, volontairement : une politique utile doit
énumérer les domaines réellement appelés, et une politique écrite à l'aveugle
casse silencieusement la carte du plan d'accès ou les avatars.

À régler une fois le site en ligne, dans cet ordre :

1. Poser d'abord la politique en **mode observation**, qui n'interdit rien :
   `Content-Security-Policy-Report-Only: default-src 'self'; …`
2. Naviguer sur tout le site, relever les violations dans la console du
   navigateur.
3. Ajouter les domaines légitimes constatés (Google Maps, Gravatar…).
4. Basculer en `Content-Security-Policy` seulement quand la console est propre.

---

## 6. Cloudflare

### Ce que c'est

Cloudflare s'intercale entre les visiteurs et l'hébergeur. Les requêtes
arrivent chez Cloudflare, qui répond directement quand il a la page en cache,
et ne transmet à l'hébergeur que le reste. C'est gratuit dans l'offre de base,
et l'apport est réel pour ce site : pare-feu, protection contre les attaques
par saturation, HTTPS, et surtout un cache réparti dans le monde — les
visiteurs ivoiriens sont servis depuis un point de présence africain plutôt que
depuis l'Europe.

### Ce que ça implique

Une chose importante : **Cloudflare devient votre gestionnaire DNS**. On ne
« branche » pas Cloudflare sur un site, on lui délègue le domaine. Concrètement
les serveurs de noms du domaine sont remplacés par ceux de Cloudflare chez le
bureau d'enregistrement. Conséquences :

- Toute modification DNS ultérieure se fait chez Cloudflare, plus chez le
  registrar ni chez l'hébergeur.
- **Les enregistrements MX doivent être recopiés avant la bascule**, sinon les
  e-mails de la mairie cessent d'arriver. C'est l'incident classique.
- Le changement de serveurs de noms se propage en quelques heures.

### Mise en place

1. Créer un compte sur <https://dash.cloudflare.com> et ajouter le domaine.
2. Cloudflare importe automatiquement les enregistrements DNS existants.
   **Vérifier un par un** que les MX et les éventuels TXT (SPF, DKIM) sont bien
   présents — c'est l'étape à ne pas bâcler.
3. Remplacer les serveurs de noms chez le bureau d'enregistrement par ceux
   indiqués par Cloudflare.
4. Attendre la confirmation d'activation.

### Réglages à appliquer ensuite

| Rubrique | Réglage | Pourquoi |
|---|---|---|
| SSL/TLS | Mode **Full (strict)** | « Flexible » chiffre seulement la moitié du trajet et provoque des boucles de redirection |
| SSL/TLS → Edge Certificates | **Always Use HTTPS** activé | Redirige tout le trafic HTTP |
| SSL/TLS → Edge Certificates | **HSTS** activé | Complète l'entête posé par le code |
| Security | Niveau **Medium** | « High » présente des captchas à des visiteurs légitimes |
| Speed → Optimization | **Brotli** activé | Compression, gain net sur connexion lente |
| Caching | Niveau **Standard** | Suffisant ; WordPress envoie les bons entêtes pour l'administration |
| Rules | Contourner le cache sur `*/wp-admin/*` et `*/wp-login.php` | Évite de servir une page d'administration en cache |

### ⚠️ Le point à ne pas manquer

Derrière Cloudflare, toutes les requêtes arrivent avec **l'adresse IP de
Cloudflare**, pas celle du visiteur. Deux protections deviennent alors nocives
si elles ne sont pas averties :

- **La limitation de nos formulaires** verrait tous les visiteurs comme une
  seule et même personne, et les bloquerait tous après cinq messages.
- **Limit Login Attempts Reloaded** bloquerait le monde entier après quelques
  échecs de connexion.

Correctif, à appliquer **le jour même de la bascule** :

Dans `wp-config.php`, avant la ligne `/* That's all, stop editing! */` :

```php
define( 'GL_IP_HEADER', 'HTTP_CF_CONNECTING_IP' );
```

Et dans **Réglages → Limit Login Attempts → Trusted IP Origins**, indiquer
`CF-Connecting-IP`.

Ce nom d'entête n'est pas deviné automatiquement par le code : un entête
accepté sans être garanti par l'hébergement se falsifie en une requête, et le
plafond ne vaudrait plus rien. Il doit donc être déclaré sciemment, une fois
Cloudflare réellement en place — et **jamais avant**.

---

## 7. Configurer les extensions installées

Trois extensions sont installées et actives, avec mises à jour automatiques.

### UpdraftPlus — sauvegardes

L'extension la plus importante du site. Une sauvegarde qui reste sur le serveur
ne sert à rien : si le serveur tombe ou est compromis, elle tombe avec lui.

1. **Réglages → UpdraftPlus → Réglages**.
2. Fichiers : **hebdomadaire**, 4 conservations. Base de données :
   **quotidienne**, 14 conservations. La base contient tout le texte rédigé,
   elle change bien plus souvent que les images.
3. Stockage distant : **Google Drive** ou **Dropbox**, sur un compte de la
   mairie — pas un compte personnel.
4. Lancer une sauvegarde manuelle et vérifier qu'elle apparaît bien à distance.
5. **Tester une restauration.** Une sauvegarde jamais restaurée n'est pas une
   sauvegarde : c'est une hypothèse. À faire sur une copie, pas en production.

### Limit Login Attempts Reloaded — force brute

Corrige l'absence totale de plafond sur `wp-login.php`.

1. **Réglages → Limit Login Attempts**.
2. 4 tentatives, blocage 20 minutes ; après 3 blocages, 24 heures.
3. Notification par e-mail après 3 blocages (nécessite le SMTP configuré).
4. Si Cloudflare est en place : renseigner `CF-Connecting-IP` dans
   **Trusted IP Origins** (voir section 6).

L'extension propose une offre payante et une connexion à son service ; ni l'une
ni l'autre n'est nécessaire.

### Two Factor — double authentification

Retenue plutôt que WP 2FA : elle est maintenue par des contributeurs du cœur de
WordPress, ne comporte aucune incitation commerciale, et se limite à ce qu'on
lui demande.

1. **Utilisateurs → Profil**, section « Two-Factor Options ».
2. Activer **Time Based One-Time Password (TOTP)**, scanner le code avec Google
   Authenticator, Authy ou FreeOTP.
3. **Générer et conserver les codes de secours** hors du site — sans eux, un
   téléphone perdu ferme définitivement l'accès.
4. À activer sur **tous les comptes Administrateur**, sans exception.

> La double authentification par e-mail suppose que le SMTP fonctionne. Tant
> qu'il n'est pas configuré, utilisez uniquement TOTP.

### Reste à installer

**WP Mail SMTP**, sans laquelle le formulaire de contact n'envoie rien. Voir la
section correspondante du [README](../README.md).

---

## 8. Checklist du jour de mise en ligne

- [ ] Domaine au nom de la mairie, renouvellement automatique activé
- [ ] HTTPS actif et forcé sur tout le site
- [ ] `WP_DEBUG` à `false`, `debug.log` supprimé
- [ ] Clés de sécurité (`salts`) régénérées
- [ ] Compte `admin` supprimé, compte nominatif + mot de passe fort
- [ ] Double authentification active sur tous les administrateurs
- [ ] Compte Éditeur créé pour l'agent + guide transmis
- [ ] `GL_IP_HEADER` et Trusted IP Origins réglés **si** Cloudflare est en place
- [ ] Enregistrements MX vérifiés après la bascule DNS
- [ ] SMTP configuré et **message de test envoyé depuis la page Contact**
- [ ] UpdraftPlus configuré, stockage distant, **restauration testée**
- [ ] Mises à jour automatiques activées (cœur et extensions)
- [ ] Permaliens reconstruits, pages Démarches et Services vérifiées
- [ ] Google Search Console et Business Profile (voir README)
- [ ] Politique de confidentialité relue — la newsletter collecte des adresses,
      traitement relevant de la loi ivoirienne n° 2013-450 et d'une déclaration
      à l'ARTCI, à faire confirmer par la mairie

## 9. Entretien courant

| Fréquence | Action |
|---|---|
| Chaque semaine | Vérifier que la sauvegarde est passée |
| Chaque mois | Appliquer les mises à jour, ouvrir le site après coup |
| Chaque trimestre | Vérifier la Search Console, tester une restauration |
| Chaque année | Vérifier l'échéance du domaine et de l'hébergement |

**Le point le plus important de tout ce document :** ce tableau doit avoir un
**nom de personne** en face. Un site institutionnel que personne n'est chargé
de mettre à jour est compromis en moins d'un an, quelle que soit la qualité du
code.
