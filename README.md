# club_management

First thing I did was to create the project with symfony using: ```sudo symfony new club_management```, inside the
route: **var/www**.

Then I gave it the respective permissions with the command: ```sudo chmod 775 club_management/ -Rf``` and
then ```
sudo chown hades.www-data club_management/ -Rf``` to give ownership to **"hades"** (my user).

At this point, I had to connect it with **nginx**, moving to ```cd /etc/nginx/sites-available``` and then create the
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
**CRUD** (Create, Read, Update, Delete). Next, I installed the forms with ```composer require symfony/form``` and made
the form **ClubType**
with ```php bin/console make:form```.

The next step I did was to install ```composer require symfony/validator```.

After this, I started the coding of the **buildForm** in **ClubType** and changed the code **ClubController** of the
function **create** to a form with a **validator**.

Later, I did all the functions of the **CRUD** with a better code and a form in create (**'POST'**) and update
(**'PUT'**), next I started the coding of the **'constraints'** on **ClubType** to make some restrictions in the
inserts.
When I finished this form, I started the update of the other **Controllers** and **Forms**. And, beginning the code so
that the **'value'** of a **'field'** is not repeated in each **Entity Class**. With this function:

``` 
public static function loadValidatorMetadata(ClassMetadata $metadata)
{
$metadata->addConstraint(new UniqueEntity([
'fields' => 'name',
'message' => 'The name already exists'
]))
```

Then, I started the coding of a new **Validator/Constraints** using ```php bin/console make:validator``` and named
**DniFormat** to make sure that the DNI inserted got
the 8 numbers and the 1 letter. Putting it in ```->add('dni', null, [
'constraints' => [``` of **PlayerType 'dni'** and **TrainerType 'dni'**.

After all, I commenced the creation of the Validator of ```PlayerSalary``` to validate if the Players' salary was higher
than the Club's budget. After that was working, I updated it to be ```SalaryValidator``` to use it with the trainers
too, and added it to **PlayerType**
and **TrainerType** forms as constraint of the **'salary'**.

Next step I made, was to create the **relation** between Club - Player, and Club - Trainer,
each one of **OneToMany**, with ```php bin/console make:entity```. Then, I started to make the functions of
**club_create_player**, **club_delete_player** and
updated the **index()**
in **ClubController**. Also, I created a function that gets the **totalSalary** of the players and trainers and other
one for the **availableBudget**, and used them on **index()** and **show_club**.

Later, I updated the **SalaryValidator** because I found an error that if you wanted to create a **new Player/Trainer**
with the function **create** with the method **'POST'** in **PlayerController/TrainerController**. It always needed the
**club** to validate it's **budget**, but it was unnecessary, because I needed to create a new player without a team.

The next thing I did, was to install ``` composer require symfony/mailer``` to use it in the function create or delete
to
send a message to the player or trainer when they got created/registered or deleted/dropped from a club.

Now, I had installed the symfony/test-pack with ```composer require --dev symfony/test-pack``` this should automatically
create **phpunit.xml.dist** and **tests/bootstrap.php**. But, If these files are missing, you can try
running ```composer recipes:install phpunit/phpunit --force -v```. And, I ran ```sudo apt install phpunit``` to install
phpunit that runs the tests.

After this is installed I ran ```php bin/console make:test``` with a **WebTestCase** to create the
**PostControllerTest** class. Next,
I had installed ```composer require --dev dama/doctrine-test-bundle```.
Then, deleted:

```when@test:
doctrine:
dbal:
# "TEST_TOKEN" is typically set by ParaTest
dbname_suffix: '_test%env(default::TEST_TOKEN)%'
```

from **config/packages/doctrine.yaml** to make the test use the actual database I've been using, and not a new
test-club_management.
Later, I've started the coding of the test, and the updates. Trying them everytime with ```phpunit``` to run them, and
see where they fail or success.

