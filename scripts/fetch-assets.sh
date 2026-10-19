#!/usr/bin/env bash
# Downloads the reusable assets listed in spec Appendix 3 from the current WordPress site
# and converts photos/logos to WebP (needs `cwebp`: brew install webp / apt install webp).
# Usage: bash scripts/fetch-assets.sh
set -u
SRC="https://dwptech.com/arabia/wp-content/uploads"
DEST="$(cd "$(dirname "$0")/.." && pwd)/public/assets/img"
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT
mkdir -p "$DEST"/{pages,partners,integrations,badges,hero,clients,team}

missing=()

# fetch <remote name(s) separated by |> <dest path relative to img/>
fetch() {
  local names="$1" out="$DEST/$2" got=""
  IFS='|' read -ra list <<< "$names"
  for n in "${list[@]}"; do
    if curl -fsL --max-time 30 -o "$TMP/dl" "$SRC/$n"; then got="$n"; break; fi
  done
  if [ -z "$got" ]; then missing+=("$2  (tried: $names)"); return; fi
  case "$out" in
    *.webp) cwebp -quiet -q 82 "$TMP/dl" -o "$out" ;;
    *) cp "$TMP/dl" "$out" ;;
  esac
  echo "✓ $2  ←  $got"
}

# Logos
fetch "DWP-Arabia-Site-Logo.png" "logo-light.png"   # white logo; a dark version must come from DWP
fetch "cropped-favicon-270x270.png" "favicon.png"

# Badges (official assets only)
fetch "Odoo-Silver-Partner-Logo.png" "badges/odoo-silver-partner.png"
fetch "Future-Factories_N.png" "badges/future-factories.png"
fetch "The-Factory-Logo.png" "badges/the-factory.png"
# SAP Partner and Huawei partner badges must come from SAP / Huawei directly — see CONFIRM.md.

# Partner logos
fetch "Sap-Logo-DWP.png" "partners/sap.webp"
fetch "Odoo-Logo-DWP-250x250.png" "partners/odoo.webp"
fetch "Microsoft-Logo-DWP-250x250.png|Microsoft-Logo-DWP.png" "partners/microsoft.webp"
fetch "Cisco-Logo-DWP-250x250.png" "partners/cisco.webp"
fetch "Huawei-Logo-DWP-250x250.png" "partners/huawei.webp"
fetch "Schneider-Electric-Logo-DWP-250x250.png|Schneider-Logo-DWP-250x250.png" "partners/schneider-electric.webp"
fetch "Fortinet-Logo-1-250x250.png" "partners/fortinet.webp"

# Saudi integrations
fetch "ZATCA.png|ZATCA-integration.png|Zatca.png" "integrations/zatca.webp"
fetch "Mudad.png|Mudad-integration.png" "integrations/mudad.webp"
fetch "GOSI.png|GOSI-integration.png" "integrations/gosi.webp"
fetch "Qiwa.png|Qiwa-integration.png" "integrations/qiwa.webp"
fetch "Salla.png|Salla-integration.png" "integrations/salla.webp"
fetch "Zid-integration.png" "integrations/zid.webp"
fetch "Foodics-integration.png" "integrations/foodics.webp"
fetch "Mada.png" "integrations/mada.webp"
fetch "STC-Pay.png" "integrations/stc-pay.webp"
fetch "Apple-Pay.png" "integrations/apple-pay.webp"
fetch "Tabby.png" "integrations/tabby.webp"
fetch "Tamara.png" "integrations/tamara.webp"

# Page images
for f in Who-We-Are-About.jpg What-We-Do-About.jpg ERP-Programs.jpg Data-and-Bi.jpg Applied-Ai.jpg CRAS.jpg What-CRAS-Solves.jpg \
         PMS.jpg What-PMS-solves.jpg Industrial-IoT-Platform.jpg HRMS.jpg Data-and-bi-Solutions.png solutions-ai.png \
         Real-time-dashboards.jpg Full-audit-trails.jpg ERP-integration.jpg; do
  slug="$(echo "${f%.*}" | tr '[:upper:]' '[:lower:]')"
  fetch "$f" "pages/$slug.webp"
done

echo
if [ ${#missing[@]} -gt 0 ]; then
  echo "Not found on the old site (upload manually — see CONFIRM.md):"
  printf '  - %s\n' "${missing[@]}"
fi
