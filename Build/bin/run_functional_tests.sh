#!/usr/bin/env bash

set -e

THIS_SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null && pwd )"
cd "$THIS_SCRIPT_DIR" || exit 1

cd ../..

./bin/phpunit -c Build/BuildEssentials/PhpUnit/FunctionalTests.xml Tests/Functional/
