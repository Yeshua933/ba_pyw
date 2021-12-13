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


# Pay Your Way Configurations...

php bin/magento config:set payment/payyourway/active 1
php bin/magento config:set payment/payyourway/title "Pay with Shop Your Way"
php bin/magento config:set payment/payyourway/environment sandbox
php bin/magento config:set payment/payyourway/merchant_name_sb MARCELTEST30
php bin/magento config:set payment/payyourway/client_id_sb MG_MARCELTEST30_QA
php bin/magento config:sensitive:set payment/payyourway/private_key_sb "-----BEGIN PRIVATE KEY----- MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCh2WED+pGfS9De VpuWdmouLRslZpZvRLA66uT9TyHNAQuzBdZZiMrd43zigg53y/4Mo6mVtrgcLgRZ 6MHAb5mOghT6Gx7UmgXG251hv/4l83TzcTV0LYujZ5VX5d29ScMWA/JZodCa0atL pxeTmf+DlEfM31I/mgWnbiL4EicROVCcpDkxPhvzdaKhXUqay+MkGh8QNUApXO+Y JlwefcphgyKwixQ/xDJTMxWXaRQcf2UDHt9hUi7tO+6DgKqEF7vF4SGZq7Me3Z4c jABtBVCczOU1PwqoiHb33IpgxOVHRgwWolBfhh237sMcM/LQyA68H2l+yGBCVWX6 BLH8oujPAgMBAAECggEAUUnw5CKxiSR3H3S4aHXJFrAo9jv0SqZhLPZVVLSt8V0Z Fy1TX1zJJ3DehJV11s1qJ8b7oepHxj+Gh/HTfmeO5HiJOKTuczF4vUwIW9QmPcFt d+fgRR2rCMM5yw/0suGdTi1lR4dyUpAETNb7jHLIVrne5hM0KCWfuVwjzByx6n/m eCNYTtbVSedq09VUdAOzRKWjI7JiC9VEde+OrnHBrbmJ7RyANzLcK1q5bQJEJJDP ENFpq+uzlosoLRhp70dFjUFi6C2eZSgqzH8HHLk0HDSsmbZ+44rSASXh0sKnLZoG hLSWdSu7/U0wVbjshsSJQ0WcVUfjjGaUEy6bs6XKgQKBgQDMK6bcEGCK5wnx5D1S 1z8Dh+CGxNWrST2IHVGbgaq+EpZRh268h0rVt4aHUT1Ckf+1HJJM9zgIVssjtB8t BUGVMc1/2QsdQ4MTGLW8L7bmkf0YNjMANVPAzHipNbKWNrrxjXXLIi6R7Dd2/FNG 5EynLBMJxG7SwbEzTPrh2Bf28QKBgQDK72eRcAPEn1Buyt/Bb62LRQyUxMgoeQf5 +wh7kKg9jwRGu93NYQhQlSZP7sl480Sk6AHtvdaVsXwIX7Fy+5t4w/5aCGyE6WUu zSHdnHfAk++uhYJga7nxB2W5GB9ENJcEhSrwvYbyqrMdr7/Nblsc1tTIyxXCOxhb aml7wZpbvwKBgQC7yKZWn0V8XBVcgrUYXVrar32F1sJDIUDT0Ut+wJs+6K+S2wKx qanduUela8XxVYEfneC9o1/I47NA6mkGKmBwjXbZ0NGVH8QNq1tzc1tA5CMpwqfT 2DhpCmcsEnRoDnyQsLAXnh/BHMbCzD735ADQfQwhnqbjdzy412OklvErsQKBgHsr XT/vrcnMLERii/Za8wkhiqZM3XN7KjU2gQqGXSanxB+ubMv3xdUrAYroUo4/kH1K d8k1PKW0iKSIeCpivhVJV/qLPFpbUldal+Bq1GAlKgdR7zTYjY3WQI5cLrX1+Wb7 8HkXf22P35D4F8D25wQU/Cc24+j0dy8c4hMECcz/AoGADVCC+RQtvOlddpdMJKzt 4qWA34BAr6xzmdqGgRyI635QbCM8tAKBTTlmfirX8/388AKbay1pqkiwIxak8g8n CkaJtQVndS1n9seLcyOtj14tp5rrgeQSj5GcK0zwJC+9u8d6NRVuwCN/ORFZqA9y bxqkyWgbX+0IvycK4Yel8cE= -----END PRIVATE KEY-----"

php bin/magento config:set payment/payyourway/secret_key_sb B6A708998FB75DD0FE03E4C3668F82148806F354992C4BB1B820516EB4219444
php bin/magento config:set payment/payyourway/payment_action authorize_capture
php bin/magento config:set payment/payyourway/sort_order NULL

php bin/magento config:sensitive:set payment/payyourway/public_key_sb "-----BEGIN CERTIFICATE----- MIIDzDCCArQCCQCs88PA6a6YdDANBgkqhkiG9w0BAQsFADCBpzELMAkGA1UEBhMC VVMxETAPBgNVBAgMCElsbGlub2lzMRgwFgYDVQQHDA9Ib2ZmbWFuIEVzdGF0ZXMx GTAXBgNVBAoMEFNlYXJzIEJyYW5kcyBMTEMxHDAaBgNVBAMME2FwcC5wYXl5b3Vy d2F5LnRlc3QxMjAwBgkqhkiG9w0BCQEWI3llc2h1YS50b3JyZWJsYW5jYUBibHVl YWNvcm5pY2kuY29tMB4XDTIxMTIxMDE5MjY1OFoXDTIyMDEwOTE5MjY1OFowgacx CzAJBgNVBAYTAlVTMREwDwYDVQQIDAhJbGxpbm9pczEYMBYGA1UEBwwPSG9mZm1h biBFc3RhdGVzMRkwFwYDVQQKDBBTZWFycyBCcmFuZHMgTExDMRwwGgYDVQQDDBNh cHAucGF5eW91cndheS50ZXN0MTIwMAYJKoZIhvcNAQkBFiN5ZXNodWEudG9ycmVi bGFuY2FAYmx1ZWFjb3JuaWNpLmNvbTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCC AQoCggEBAKHZYQP6kZ9L0N5Wm5Z2ai4tGyVmlm9EsDrq5P1PIc0BC7MF1lmIyt3j fOKCDnfL/gyjqZW2uBwuBFnowcBvmY6CFPobHtSaBcbbnWG//iXzdPNxNXQti6Nn lVfl3b1JwxYD8lmh0JrRq0unF5OZ/4OUR8zfUj+aBaduIvgSJxE5UJykOTE+G/N1 oqFdSprL4yQaHxA1QClc75gmXB59ymGDIrCLFD/EMlMzFZdpFBx/ZQMe32FSLu07 7oOAqoQXu8XhIZmrsx7dnhyMAG0FUJzM5TU/CqiIdvfcimDE5UdGDBaiUF+GHbfu wxwz8tDIDrwfaX7IYEJVZfoEsfyi6M8CAwEAATANBgkqhkiG9w0BAQsFAAOCAQEA jmHP6QJ3CVJvBDzDaCm04hEmvKC9C9pBVqMd4tOZj9zN1Em8MHDACU2EcuQV3Fvt bV4IOsSCQdRVF8aNvmXAs8eNB/mza07kq6HxRuEqyvJSuEY/SUMyLvsdLcZq/DHv laFHcQwtKyCX5JvAXZJzLgNbtrxIhL951mDP8egg6gtcQuSvbLFBUaWf+A3CJLfZ xD5LGvWi1TIRJAP/6g0d1ATLuio1DctDfTsgtDwNyPTj2oO4zXGBndE/ETxCJuYg AEbswVPM18IThi3qm9/5sIOj2heYs1f3w4o2J9Hzez5rgqU4D7R5ZcC2/IDg/tyh YiyK6dWQjjEgYcxAornTmA== -----END CERTIFICATE-----"
php bin/magento config:set payment/payyourway/merchant_email_sb test@blueacornici.com
php bin/magento config:set payment/payyourway/merchant_phone_sb 7137777777
php bin/magento config:set payment/payyourway/merchat_category_sb 0
php bin/magento config:set payment/payyourway/merchant_state_sb TX
php bin/magento config:set payment/payyourway/merchant_zip_sb 77007
php bin/magento config:set payment/payyourway/merchant_address_sb "4400 Memorial Dr"
php bin/magento config:set payment/payyourway/merchant_city_sb "Houston"
php bin/magento config:set payment/payyourway/debug 1


# Flush cache
php bin/magento cache:flush
