

## Education Coding Challenge

## Requirements

* Installation with Composer
    - [PHP 7 or later](https://www.php.net/manual/en/install.php).
    - [Composer](https://getcomposer.org/).
    - [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).

1. Run git clone <my-cool-project>
2. Run composer install
3. Run cp .env.example .env
4. Run php artisan key:generate

* Installation with Docker
    - [Docker Desktop](https://docs.docker.com/desktop/).

1. Run git clone <my-cool-project>
2. Run docker-compose up -d
3. Run docker exec -it toy_robot_app bash

## Instructions


In the main directory of the project run 

    > php artisan command:toy_robot

Follow the instruction at the promp.

For usind command files there are some .txt examples at:

    > toyrobot-app/storage/app/public

**Note**

A config file in:

    > toyrobot-app/config/toyrobot.php 
    
allows you to choose the dimensions of the board

