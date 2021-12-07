# Turn off `Magento_TwoFactorAuth`
php bin/magento module:disable Magento_TwoFactorAuth

# Flush cache
php bin/magento cache:flush

# Re-compile DI
php bin/magento setup:di:compile

# Create admin account for Marcel
php bin/magento admin:user:create \
--admin-user=marcel \
--admin-password=marcel123 \
--admin-email=marcel.martinez@blueacornici.com \
--admin-firstname=Marcel \
--admin-lastname=Martinez

# Create admin account for Dean
php bin/magento admin:user:create \
--admin-user=dean \
--admin-password=dean123 \
--admin-email=dean.pekecnpaugh@blueacornici.com \
--admin-firstname=Dean \
--admin-lastname=Dean

# Create admin account for Jim
php bin/magento admin:user:create \
--admin-user=jim \
--admin-password=jim123 \
--admin-email=jim.pier@blueacornici.com \
--admin-firstname=Jim \
--admin-lastname=Pier

# Create admin account for Vinayak
php bin/magento admin:user:create \
--admin-user=vinayak \
--admin-password=vinayak123 \
--admin-email=vinayak.hedge@blueacornici.com \
--admin-firstname=Vinayak \
--admin-lastname=Hedge

# Create admin account for Rohan
php bin/magento admin:user:create \
--admin-user=Rohan \
--admin-password=rohan123 \
--admin-email=rohan.gandhi@blueacornici.com \
--admin-firstname=Rohan \
--admin-lastname=Gandhi

# Create admin account for Aaron
php bin/magento admin:user:create \
--admin-user=aaron \
--admin-password=aaron123 \
--admin-email=aaron.prakash@blueacornici.com \
--admin-firstname=Aaron \
--admin-lastname=Prakash

# Create admin account for Ganesh
php bin/magento admin:user:create \
--admin-user=ganesh \
--admin-password=ganesh123 \
--admin-email=ganesh.venkatachalam@blueacornici.com \
--admin-firstname=Ganesh \
--admin-lastname=Venkatachalam

# Create admin account for Greg
php bin/magento admin:user:create \
--admin-user=greg \
--admin-password=greg123 \
--admin-email=greg.pogue@blueacornici.com \
--admin-firstname=Greg \
--admin-lastname=Pogue

# Create admin account for Eui
php bin/magento admin:user:create \
--admin-user=eui \
--admin-password=eui123 \
--admin-email=eui.chung@blueacornici.com \
--admin-firstname=Eui \
--admin-lastname=Chung

# Create admin account for Sindura
php bin/magento admin:user:create \
--admin-user=sindura \
--admin-password=sindura123 \
--admin-email=sindura.angadimani@blueacornici.com \
--admin-firstname=Sindura \
--admin-lastname=Angadimani

# Create admin account for Amol
php bin/magento admin:user:create \
--admin-user=amol \
--admin-password=amol123 \
--admin-email=amol.bhangale@syw.com \
--admin-firstname=amol \
--admin-lastname=bhangale

# Create admin account for Alkesh
php bin/magento admin:user:create \
--admin-user=alkesh \
--admin-password=alkesh123 \
--admin-email=alkeshmohandas.prabhudesai@syw.com \
--admin-firstname=Sindura \
--admin-lastname=Prabhidesai

# Create admin account for Jenny
php bin/magento admin:user:create \
--admin-user=jenny \
--admin-password=jenny123 \
--admin-email=jenny.kishazy@syw.com \
--admin-firstname=jenny \
--admin-lastname=kishazy

# Create admin account for Mehul
php bin/magento admin:user:create \
--admin-user=mehul \
--admin-password=mehul123 \
--admin-email=mehul.parekh@syw.com \
--admin-firstname=mehul \
--admin-lastname=Parekh

# Flush cache
php bin/magento cache:flush
