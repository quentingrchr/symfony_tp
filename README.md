# Start
- Clone the project
- Make sure you got the right .env
- ````composer install````
- ````docker compose up -d````
- ````symfony console doctrine:migrations:migrate ````
- ````symfony console doctrine:fixtures:load ````
- ````symfony server:start -d````
- Launch http://localhost:8000 on your browser
