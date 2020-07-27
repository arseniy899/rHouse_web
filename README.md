# rHouse_web
 Web interface to control your R-House (Smart home)
## Requirements
It is recommended to start with the main repository to setup HUB.
# Setup enviroment
This project is made to be run on single-board computers (Raspberry PI, Banana PI, etc.). So, it is recommended to use Linux distros, like Raspbian.

On Raspbian, run the following to install required packages (Apache and MySQL):
```
sudo apt-get install -y can-utils default-libmysqlclient-dev apache2 php7.0 php-mbstring php-zip php-curl libapache2-mod-php  mariadb-server php-mysql 
cd /var/www/html
git clone git@github.com:arseniy899/rHouse_web.git
```
## Config instance
Go to your machine through Web-Broswer (HTTP port). You will be redirected to setup wizard.

When wizard finishes, a file `config.php` will be created in the root of `/var/www/html`, redirecting to the latest installed version.

### config.php
Values in config file:

`userPolicy (true|false)` - should web-broswer apply users-policy and restirct non-authed access. All users can be managed by admins in admin panel.

`version v(d.dd[l[d]])` - currenlty selected version of web interface

## To do
- [ ] Tests coverage
- [ ] Docs
- [ ] Language-translation of user interface
<img src="https://raw.githubusercontent.com/arseniy899/rHouse_web/master/screenshots/rho_web/panel_screen.jpg" width="25%" >
<img src="https://raw.githubusercontent.com/arseniy899/rHouse_web/master/screenshots/rho_web/admin_1.png" width="25%" >
<img src="https://raw.githubusercontent.com/arseniy899/rHouse_web/master/screenshots/rho_web/admin_2.png" width="25%" >
<img src="https://raw.githubusercontent.com/arseniy899/rHouse_web/master/screenshots/rho_web/admin_3.png" width="25%" >
<img src="https://raw.githubusercontent.com/arseniy899/rHouse_web/master/screenshots/rho_web/admin_4.png" width="25%" >
<img src="https://raw.githubusercontent.com/arseniy899/rHouse_web/master/screenshots/rho_web/admin_5.png" width="25%" >
