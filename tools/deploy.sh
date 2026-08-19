#!/usr/bin/env bash
#
# Déploiement du code vers le serveur.
#
# Envoie le thème et le socle métier, puis vide le cache. Ne touche jamais à la
# base, aux images téléversées ni aux extensions : ceux-là vivent sur le
# serveur et un déploiement de code ne peut pas les écraser.
#
#   ./tools/deploy.sh            simulation, confirmation, puis envoi
#   ./tools/deploy.sh --check    simulation seule, rien n'est envoyé
#   ./tools/deploy.sh --yes      sans confirmation (pour un usage automatisé)
#   ./tools/deploy.sh --rewrites reconstruit aussi les permaliens
#
# Le dernier n'est nécessaire que si un type de contenu ou un slug a changé.
#
set -euo pipefail

# Réglages. Modifiables sans toucher au script : GL_HOTE=autre ./tools/deploy.sh
HOTE="${GL_HOTE:-lahou}"
RACINE_DISTANTE="${GL_RACINE:-~/sites/lahou.ktim.site}"
URL="${GL_URL:-https://lahou.ktim.site}"
WPCLI="${GL_WPCLI:-wp-cli_php8.3}"

LOCAL="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

simulation=0
confirmer=1
rewrites=0
for arg in "$@"; do
  case "$arg" in
    --check)    simulation=1 ;;
    --yes|-y)   confirmer=0 ;;
    --rewrites) rewrites=1 ;;
    -h|--help)  awk 'NR>2 && /^#/ { sub(/^# ?/, ""); print; next } NR>2 { exit }' "${BASH_SOURCE[0]}"; exit 0 ;;
    *) echo "Option inconnue : $arg" >&2; exit 2 ;;
  esac
done

info() { printf '\033[36m▸ %s\033[0m\n' "$1"; }
ok()   { printf '\033[32m✓ %s\033[0m\n' "$1"; }
warn() { printf '\033[33m! %s\033[0m\n' "$1"; }

# --- Contrôles avant envoi --------------------------------------------------

for dossier in wp-content/themes/grand-lahou wp-content/mu-plugins; do
  [ -d "$LOCAL/$dossier" ] || { echo "Dossier introuvable : $LOCAL/$dossier" >&2; exit 1; }
done

# Du code non commité déployé, c'est une version en ligne que personne ne peut
# retrouver dans l'historique. On prévient plutôt que d'interdire : tester une
# correction avant de la figer est légitime.
if [ -n "$(git -C "$LOCAL" status --porcelain 2>/dev/null)" ]; then
  warn "Des modifications ne sont pas commitées — le serveur recevra du code absent de l'historique."
fi

info "Vérification de la connexion à « $HOTE »…"
version="$(ssh -o ConnectTimeout=10 "$HOTE" "cd $RACINE_DISTANTE && $WPCLI core version" 2>/dev/null || true)"
[ -n "$version" ] || { echo "Connexion impossible, ou WordPress introuvable dans $RACINE_DISTANTE." >&2; exit 1; }
ok "WordPress $version sur $HOTE"

# --- Simulation -------------------------------------------------------------

RSYNC=(rsync -az --delete --exclude '.DS_Store' --exclude '*.map')

envoyer() { # $1 = dossier local relatif, $2 = mode
  local src="$LOCAL/$1/" dst="$HOTE:$RACINE_DISTANTE/$1/"
  if [ "$2" = "simulation" ]; then
    "${RSYNC[@]}" -vn --out-format='  %o %n' "$src" "$dst" | grep -E '^  (send|del\.) ' | grep -v '/$' || true
  else
    "${RSYNC[@]}" -v --out-format='  %o %n' "$src" "$dst" | grep -E '^  (send|del\.) ' | grep -v '/$' || true
  fi
}

info "Ce qui serait envoyé :"
apercu="$(envoyer wp-content/themes/grand-lahou simulation; envoyer wp-content/mu-plugins simulation)"

if [ -z "$apercu" ]; then
  ok "Le serveur est déjà à jour, rien à faire."
  exit 0
fi

echo "$apercu"

# Une suppression côté serveur mérite un regard : c'est le seul geste de ce
# script qui puisse faire disparaître quelque chose.
if echo "$apercu" | grep -q '^  del\.'; then
  warn "Des fichiers seront SUPPRIMÉS sur le serveur (lignes « del. » ci-dessus)."
fi

[ "$simulation" -eq 1 ] && { ok "Simulation terminée, rien n'a été envoyé."; exit 0; }

if [ "$confirmer" -eq 1 ]; then
  printf 'Envoyer ? [o/N] '
  read -r reponse < /dev/tty
  case "$reponse" in [oOyY]*) ;; *) echo "Annulé."; exit 0 ;; esac
fi

# --- Envoi ------------------------------------------------------------------

info "Envoi du thème…"
envoyer wp-content/themes/grand-lahou reel > /dev/null
info "Envoi du socle métier…"
envoyer wp-content/mu-plugins reel > /dev/null
ok "Fichiers en place"

if [ "$rewrites" -eq 1 ]; then
  info "Reconstruction des permaliens…"
  ssh "$HOTE" "cd $RACINE_DISTANTE && $WPCLI rewrite flush --hard" >/dev/null
  ok "Permaliens reconstruits"
fi

info "Vidage du cache…"
ssh "$HOTE" "cd $RACINE_DISTANTE && $WPCLI cache flush" >/dev/null
ok "Cache vidé"

# --- Vérification -----------------------------------------------------------

code="$(curl -s -o /dev/null -w '%{http_code}' "$URL/")"
ver="$(curl -s "$URL/" | grep -o 'theme\.css?ver=[0-9]*' | head -1)"
if [ "$code" = "200" ]; then
  ok "$URL répond (HTTP $code) — $ver"
else
  warn "$URL répond HTTP $code — à vérifier."
fi

if [ "$rewrites" -eq 0 ]; then
  echo
  echo "Si vous avez changé un slug ou ajouté un type de contenu, relancez avec --rewrites."
fi
