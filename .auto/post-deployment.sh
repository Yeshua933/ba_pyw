# Turn off `Magento_TwoFactorAuth`
php bin/magento module:disable Magento_TwoFactorAuth

# Flush cache
php bin/magento cache:flush

# Re-compile DI
php bin/magento setup:di:compile

# Create admin account for Myles
php bin/magento admin:user:create \
--admin-user=myles \
--admin-password=changem3! \
--admin-email=myles.forrest@blueacornici.com \
--admin-firstname=Myles \
--admin-lastname=Forrest

# Create admin account for Iryna
php bin/magento admin:user:create \
--admin-user=iryna \
--admin-password=changem3! \
--admin-email=iryna.malikova@blueacornici.com \
--admin-firstname=iryna \
--admin-lastname=malikova

# Create admin account for Katrina
php bin/magento admin:user:create \
--admin-user=katrina \
--admin-password=changem3! \
--admin-email=katrina.lewismoss@blueacornici.com \
--admin-firstname=katrina \
--admin-lastname=lewismoss

# Flush cache
php bin/magento cache:flush
