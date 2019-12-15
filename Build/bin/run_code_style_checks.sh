#!/usr/bin/env bash

set -e

THIS_SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd )"
cd "$THIS_SCRIPT_DIR" || exit 1

cd ../..

php bin/phpcs --config-set installed_paths Packages/Libraries/de-swebhosting/php-codestyle/PhpCodeSniffer/

php bin/phpcs --standard=PSRDefault Classes Tests
