# club_management

First thing I did was to create the project with symfony using: ```sudo symfony new club_management```, inside the
route: **var/www**.

Then I gave it the respective permissions with the command: ```sudo chmod 775 club_management/ -Rf``` and
then ```
sudo chown hades.www-data club_management/ -Rf``` to give ownership to **"hades"** (my user).

At this point, I have to connect it with **nginx**, moving to ```cd /etc/nginx/sites-available``` and then create the
document
with ```sudo
vim club_management```. Inside this doc I pasted the next code bloc for this purpose:

```
server {
# Example PHP Nginx FPM config file
listen 80 ;
listen [::]:80 ;
server_name club.localhost;
root /var/www/club_management/public;
location / {
# try to serve file directly, fallback to index.php
try_files $uri /index.php$is_args$args;
}

    # optionally disable falling back to PHP script for the asset directories;
    # nginx will return a 404 error when files are not found instead of passing the
    # request to Symfony (improves performance but Symfony's 404 page is not displayed)
    # location /bundles {
    #     try_files $uri =404;
    # }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;

        # optionally set the value of the environment variables used in the application
        # fastcgi_param APP_ENV prod;
        # fastcgi_param APP_SECRET <app-secret-id>;
        # fastcgi_param DATABASE_URL "mysql://db_user:db_pass@host:3306/db_name";

        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP's OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        # Caveat: When PHP-FPM is hosted on a different machine from nginx
        #         $realpath_root may not resolve as you expect! In this case try using
        #         $document_root instead.
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Remove the internal directive to allow URIs like this
        internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }
}
```

The next step I did was to add this doc to ```cd ../sites-enabled```
with ```sudo ln -s ../sites-available/club_management```. Then ```ls``` to see if the doc is there, and
run ```sudo nginx -t``` and ```sudo service nginx reload``` to start the service.

At this point, I had **nginx** running and decided to open the project with **PHPStorm**, here I used the terminal to
run
symfony
services with ```symfony serve -d```.

After that, I ran ```composer require symfony/orm-pack``` to install the orm-pack from symfony, then opened the new
created ```.env``` and
pasted: ```DATABASE_URL="mysql://root:root@127.0.0.1:3306/club_management?serverVersion=5.7"``` at the bottom, to
connect
it with **MySQL**.

Later, I had created the database with ```php bin/console doctrine:database:create```. Then, I had to install the
maker-bundle from symfony with ```composer require --dev symfony/maker-bundle```.

At this point, I started the creation of the entities **'Club', 'Players', 'Trainers'**
with ```php bin/console make:entity``` and giving them their respective attributes, like the **'name', 'email', 'dni',
etc**.

After creating the entities I had to migrate them to my MySQL database. First with ```php bin/console make:migration```
to create the migration and second ```php bin/console doctrine:migrations:migrate``` to migrate it.

Then, I created the controllers manually in the dir **src/Controller** and started coding the
**CRUD**. Next, I installed the forms with ```composer require symfony/form``` and made the form **ClubType**
with ```php bin/console make:form```.

The next step I did was to install ```composer require symfony/validator```.

After this, I started the coding of the **buildForm** in **ClubType** and changed the code **ClubController** of the
function **create** to a form with a **validator**.

Later, I did all the functions of the **CRUD** with a better code and a form in create (**'POST'**) and update
(**'PUT'**), next I started to coding the **'constraints'** on **ClubType** to make some restrictions in the inserts.
When I finished this form, I started the update of the other **Controllers** and **Forms**. And, I started the code so
that the **'value'** of a **'field'** is not repeated, in each **Entity**, with this function:

``` 
public static function loadValidatorMetadata(ClassMetadata $metadata)
{
$metadata->addConstraint(new UniqueEntity([
'fields' => 'name',
'message' => 'The name is already exists'
]))
```

Then, I started the coding of a new **Validator/Constraints** named **DniFormat** to make sure that the DNI inserted got
the 8 numbers and the 1 letter, and added it in ```->add('dni', null, [
'constraints' => [``` of **PlayerType 'dni'** and **TrainerType 'dni'**.



