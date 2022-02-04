#### Hetic student project
#### App like "Le Bon Coin", using Symfony and Twig templating

# Start the project
- Clone the project
- Make sure you got the right .env
- ````composer install````
- ````docker compose up -d````
- ````symfony console doctrine:migrations:migrate ````
- ````symfony console doctrine:fixtures:load ````
- ````symfony server:start -d````
- Launch http://localhost:8000 on your browser

# Informations

- To login with users created by Fixtures: the password is "password", check the mail in public.user table
- Admin users can do anything (post, draft, delete all announces)
- Other logged user can only handle their announces
