#!/bin/zsh
set -euo pipefail

script_dir="${0:A:h}"
project_root="${script_dir:h}"
cd "$project_root"

pause_before_close() {
  if [[ -t 0 ]]; then
    echo "Press Enter to close this window."
    read -r _ || true
  fi
}

items=(
  banners
  css
  images
  js
  modules
  php
  efecade.js
  favicon.ico
  index.php
  prcsFm.php
  press.php
  robots.txt
  sitemap.php
  sitemap.xml
)

timestamp="$(date +%Y%m%d-%H%M%S)"
zip_name="${ZIP_NAME:-fernandod-compressed-deploy-$timestamp.zip}"
zip_path="${ZIP_PATH:-$script_dir/$zip_name}"

missing_items=()
for item in "${items[@]}"; do
  if [[ ! -e "$item" ]]; then
    missing_items+=("$item")
  fi
done

if (( ${#missing_items[@]} > 0 )); then
  echo "Cannot create the zip because these items are missing:"
  printf '  - %s\n' "${missing_items[@]}"
  echo
  pause_before_close
  exit 1
fi

if [[ -e "$zip_path" ]]; then
  echo "Cannot create the zip because this file already exists:"
  echo "$zip_path"
  echo
  pause_before_close
  exit 1
fi

echo "Creating zip:"
echo "$zip_path"
echo

/usr/bin/zip -rq "$zip_path" "${items[@]}" -x '*.DS_Store' '*/.DS_Store'

echo
echo "Zip created successfully."
pause_before_close
