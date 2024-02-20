# System requirement
Docker and docker compose

# Build local environment
### For the first time
1. Clone source code from Git and make sure that there are at least 2 folders named as docker and api in the same root directory.
2. Open the docker folder.
3. Check the content of `.env.local` file. You can update it if needed.
4. Run below command in the terminal to set up the application for the first time.
- `sh deploy.local.sh`
5. Access the application at `http://localhost` when the above step is finished.

### Start an existed system (from the 2nd time)
1. Open the docker folder and run below command in the terminal.
- `docker compose start`

# Useful tips:
### Start an interactive Bash shell in a Docker container
Run `docker compose exec {serviceName} bash`. For example: `docker compose exec api bash`

### Add a composer package
Run `docker compose exec api composer require {packageName}`. For example: `docker compose exec api composer require phpmd/phpmd`

### Stop server
Run: `docker compose stop`