:: Run easy-coding-standard (ecs) via this batch file inside your IDE e.g. PhpStorm (Windows only)
:: Install inside PhpStorm the  "Batch Script Support" plugin
cd..
cd..
cd..
cd..
cd..
cd..
vendor\bin\ecs check vendor/markocupic/backend-password-recovery-bundle/src/Resources/contao --fix --config vendor/markocupic/backend-password-recovery-bundle/.ecs/config/legacy.php
cd vendor/markocupic/backend-password-recovery-bundle/.ecs./batch/fix