#!/usr/bin/env bash
echo "Updating npm..."
npm update
echo "Installing npm packages..."
npm install
echo "Updating composer..."
composer update
echo "Fixing vulnerabilities..."
npm audit fix
echo "Regenerating IDE Helper..."
./ide_helper_regen.sh
echo "Refreshing autoload..."
./refresh_autoload.sh
echo "Compiling..."
npm run dev -- --env.full true