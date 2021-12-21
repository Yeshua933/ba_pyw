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

# Create admin account for Amol
php bin/magento admin:user:create \
--admin-user=amol \
--admin-password=amol123 \
--admin-email=amol@syw.com \
--admin-firstname=amol \
--admin-lastname=amol


# Pay Your Way Configurations...

php bin/magento config:set payment/payyourway/active 1
php bin/magento config:set payment/payyourway/title "Pay with Shop Your Way"
php bin/magento config:set payment/payyourway/environment sandbox
php bin/magento config:set payment/payyourway/merchant_name_sb MARCELTEST30
php bin/magento config:set payment/payyourway/client_id_sb MG_MARCELTEST30_QA
php bin/magento config:set payment/payyourway/secret_key_sb B6A708998FB75DD0FE03E4C3668F82148806F354992C4BB1B820516EB4219444
php bin/magento config:set payment/payyourway/payment_action authorize_capture
php bin/magento config:set payment/payyourway/sort_order NULL
php bin/magento config:set payment/payyourway/merchant_email_sb test@blueacornici.com
php bin/magento config:set payment/payyourway/merchant_phone_sb 7137777777
php bin/magento config:set payment/payyourway/merchant_category_sb 0
php bin/magento config:set payment/payyourway/merchant_state_sb TX
php bin/magento config:set payment/payyourway/merchant_zip_sb 77007
php bin/magento config:set payment/payyourway/merchant_address_sb "4400 Memorial Dr"
php bin/magento config:set payment/payyourway/merchant_city_sb "Houston"
php bin/magento config:set payment/payyourway/debug 1

php bin/magento config:set payment/payyourway/private_key_sb 0:3:HsmjauTKV0Jh4wPf6fWswpsV9dlTt7jmSH3wFJ9oSKSKk7gZM2ssnU3KPQa4/vzIEj19QxBrzeO2iXJ/cCUpfpyqdGEbsYtEIUzCRoe8chkwp1QNolfQAueB5acr0/n3E3o/5KRoIhpaMNRhhfXf4jCtL5LDKpKv0e3128BkcYfS4iySCo070axC8aQhK+0RvH1aMfBrB3g1Ji+O+HVeIXdqC1j3VHkx6jLbm3PPxyMwQCOA6zP6bAdE9Un6OO2DLIx4QPuDFhefccH5nHYb8IPQx/lpQiZUKw4xhpUU0tyMM1TszPSQHtkRWXJDLatVnf0fOZFoFBFMYXH1jhyO3B76PfRUCfelA5z9kbuEFHkW16Rk7CNFh7qS9wUmV4/lZWP0GqdgJ1yBbH3KhFLjFQWUL0BdrXVFIlsfX/o1Pkvq7g3GlXSVBROcqAvDFlUoCO+hm1EIbhItSxqOHL8ez7XzLaNAmkIiqzI4Ej4sG3ZYXhZon7jFL5WOK9YeKjvnJs/+z2QoSmzWmPfogjl1qLw7JzSiH09npZjCNI0X4eyjbAje3IzqCOAV5oP6ppiIrKGLJNe0/eQZh23iVCJ5xuUq7iN+f0uA/t5wUsHM2pcBw/qu9nh7PAIpf1PRwDZ4Z/bvuJ7hY6ptrADj6xdzQFD5xkK6UzAECxjRDYKTGJ9GbcDqOxVFIgH07yyKRz9JRbO/ehKmPdCrx2okbEgmGc65IcQc2mtwBvbLE2iuPaURGWCdrZo2qTHOFxoDAIWpP+MrA+h/JB5R2nxnXLIXq4gbFwvng0WP9HmzPBpKoTiepwI6l4phoIdLrfmPoAnvfxD04DGpnWphx95xVe4FD2q4oeMoYZxQCKPtv0l8+Qtte+lNsnvhMBUkktg6gzM/gocN9icyGgX3nUMK7ZZqzFwKI3/pc/YsUVwEdRYg8OxaK5yXBkvTzpGgbWq++Hv54KrIcCJ6zVZJysdGVdHvTkTIZP3VrORbYS56xPJnfOjAlUsnrdvYjyvom4hp2niiwdSDuFe7LMnrCsdWakM9S8vM0Qnwiuvk2ekDJnihPTiRU3mw2ib4sKEjgt5UIu11Cl8gzSfZf8xz6Hr4MHlyJGKniWZEL/quDYeVSZFRHJI/uHoTSgy9oavgMhwGOHp6Hhy+osUFZS9b7B3N6LPjMNcFf1rzEj7PJYaktUiEZfRu9mVl5xmDlNGWZt7p3q84isbk56Sv6FW9B+BClwwuZgLvJxvlMDdl3jM+p0krXZHqLbi+R8lwI3Y3AJ+Donr68OAUmm7NMIGz8YseTuJQiiekrgkapqvzXtQEf1Ddg3TYF6Dv0w+OHl2wu+5MN19BHDewU/IxDSrvdqo2qc7Yoq8few8tUkBKV+nadpQHKhbJnHaOp77OYnJFH6CjHMhqlO9LMsy3+JFdteTyZ+AcaXNCj0NwWNKfohpija2Vrw6vSOd8+mc77B//Pc2vIwZRcHn3AyTtZATu2+un7vKdznDxnrvAcdMM3Nkx9iujEhHRjX9JeFYIdgx4+flNsjkWSTIMGUYkEX6F/jo/tLHhQp2TupsVIymoHrVV3F0qW8NZXYvzbvQF2pJnU33HiYPGYbBufhPaN1D0opAGDP07DtQww5LhVukH0tjAFKWd6fhczYPF5eZMZQZ+SIN4Mt6OgOZfgOAcBFjfaNRP/brBNLGeaxKyvh+kkUhCSAtLwPnonTm6574DM1G/f5dxwg6f5WDvECLVKUWzrtYRXQwqHPqYr2l3IPKSCfh8+ms8tAD+OiJbXX1wEORkEed7PBq501JUmpwSK840nZEWIzPW1akOYdDNz6n+DGNvRmFVyR4wEXcMwHd6CiOsEl3X/zn+a3tC03Xzy76Wg8JoRa64RkzFD+qOQCjpASsg6degmJPA+1XOhkFiqDE5rxTkKCKOnr05pFItD7EmZRw8h9Qm4jbv9Gkpg4DoNe2m2N2ffpxz439hs04smBYhCRgtNr7d6Ole7DYFQtEoMGEVrx1UyqRx5KlAlphTwbTF3uPv9PqVPdK/cnfIPhy0x4zYZ/o/DgAaf/801T5fTYiiRztxTz5uppRTRB6B8nLdi1m8HKFGq0H6XaFtaebbVdpmZZir3ExZZXbACivaUvOXcdmXuFHwY2T3qjAz5dXhzLsLxgZSvXpqx5gk2k7ZiB4Dl8OWaiom6xP5Zf3MpiHRJCyoKju/bW6hyBeOfqbBfEV8g/MqBuSREKciRPOXpCVp+VYBiLYqDArk+yeise4JQ485DRuwURv2MMLa2do2o0xelMHLzQc8UuLiPBEyqn1hzfZ5ncEF
php bin/magento config:set payment/payyourway/public_key_sb 0:3:fxTN9GajpoJiI3jXfUsfxQ9sXPq1Co0zRFRtKNmkvlkAM1ZqlBSb8jO3ipeCDVA7CxKpmm10AWbdKp+tDHWUOirxTkIiPsvb/YlY1A5lQBhFIHxyVCHDmL6lo1Kei0eYbcpacK+rrmHRyTyVbOTC0ExaDo5Rq2oWRC/D8EPmNRfZB9MqM14YdoD/f5we11oATftClbf9eaZnlgxoHbfFnOGqLd/9w7mRoIrrD1zeYBg3x3bazeNbPTr85wqksAyeVJRsNEmKLEXgISjAcUfIVbGMwFfyAEXZczu3QwINGWR3xpFfHJizxg054AjF0Vin/L4ED9h/Qp8y4u11cGKVRR+l+RL/5M5IeBz8rZTXagkh1ppy6nF44K/vd84+NdYIyluBi6NRLsdkVA4dtatJ+zwfAnFm0Xqcy64BeQdvkVBQ02OW/SPakhelQUk9JFG8nHy3NM18w5Ryo0lrJxSSfyMeSob7kQgMS6lY9PihpXMAwR6wuXby9DG088s3fNPQ7nn5NNQLLLi1YPs40KcGtqXRiMC/M1iWqLnsow986XzILZep2K1DfLtup9YB+RTDd5FAySlAhRNk9M1R/6kuvlRebSbF39OoD421Tr7ctYKchPpn6R04BAc4ZTOeR/XHbIZk420lB3txQxIGMpfuRHNp6/tgiqpC+dSn41rjGteM6JZ4gkeF3wN/PLmpIn6ZIYD3WbrRklkv7M4L/IQ04hLTYlZfCQX5M1mKH2ARsZwGBt3EvHdo+oA1JNypyjkYI26anaTUXemlCqOHp3yZmYMhODZJXB2ftNcQUqo+EGnjGgzz9CGXoE0I4SFu69OMcSz9J+nayGbPafEgM59SGueQc9wvf1RNVpQ1713GWHH0DNeCZlHIjTpm31ZRPmAy1AgpxcUI3ZFyl1/k+iaTT/IlGAN/y1Y0kPTr4fuTl3mUxlpkNf7+sfmYEvzdCiIlWphqL7E2SJuPbCkm3XI7hP+uyzVhzl7YB1t3UFu6+PY/sa5p6hANYClpHQ6vAhIq2CgVRCI2Ii+bfkDVtkuyA6r3CA9/1+zKL2nRrXC88KwgmsMoxaRou3H4uZ7ZBafmzlh73yHRiTRJ1NlM4axlv46wVdgar/MQ2F15uhze5513cdDz17ML/lu0dN5qwoVv4JbzmpEzinoAOX4LDqKX7lQrQNa8QpD8KXm85YsFD30iMl6VDNdsXgdmBTSHKcbSDjx9PDDbfpp6WapFaNK+KLBeZTpc/F3ycrlqhGp5g2AwVn6Rt6fqbITPe6aPwhd33vP9d5/RN4FSX1IGnKgzwn03HxQqwuLdpGBWF9HySajYo7waa+Vw+ocXwBfatgcyfqEMFOJKcmewud5GVaVorF1AcDv4Ku+Cg+7lF8jtwf44iq9l2aQf6E3Fk9DBqVyaH7AO7jnPldRe94K/QkIEhWHqF4mNBgx67Vb6PUj3QYvoVA8Tj43d3/4Vz6TSTTHd+4YYjclqDqbzk3j4CiJPpgVcqNcQtjCpZfYzAgC6KkTHs22IgeWmW/gSiExErERBYb8a75CMxEyhzGIj/5otr///nDREuo+PIDJY69hUpUhzp8kf5qimCkw7A/feTd2C7NN4R5SJPWabXbW5CopAbROwc1wU54EjZrUJudtUKpexLvQFt/IOXvAQ71y0LpfhiDxi+zZMqPhqAr4KHa6KXCYPiuhqTs4pwIHu3IR7kqUM/8VyaDpgZGZBwhJwrqMLmERWc7DGoS7J+68P1OYCtQwE4qSeoqc1tLC8lQkDOjOS4idTj7kxuTySsXUo5OzWSW01RxVPHAEMc1TWcbXkMbzi59tsgVEOzxjCgyLGOjQ96VmupnMT60UPudJpWscgrXcaZIR790VJZGpVhbA=



# Flush cache
php bin/magento cache:flush
