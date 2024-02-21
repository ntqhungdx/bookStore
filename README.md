# Overview

Develop an application with Laravel that provides a search API for end-users. It is essential for the business so their customers can find their books in less than a seconds.

Specifications:

- The system should have a single api endpoint like <http://bookreaders.com.au/search/book?q={keyword}>
- The `keyword` can be a `title`, `summary`, `publisher` or `authors`.
- The final JSON data model for a response should contain these values:

```json
{
    "id": 1234,
    "publisher": "Packt",
    "title": "Mastering Something",
    "summary": "some long summary",
    "authors": [
        "Author One",
        "Author Two"
    ]
}
```

- The project should have units and integrations tests.
- Dockerize your project and make sure that it will work out of the box.
- Put your code on Github or Gitlab.
- Catch all edge cases and aim for the best run-time possible.
- You are free to use any architecture, design, and implementation method, but not external on-premise or cloud services.

## System requirement

Docker and docker compose

## Build local environment

### For the first time

- Clone source code from Git and make sure that there are at least 2 folders named as docker and api in the same root directory.
- Open the docker folder.
- Check the content of `.env.local` file. You can update it if needed.
- Run below command in the terminal to set up the application for the first time.

```sh
  sh deploy.local.sh
```

- Access the application at `http://localhost` when the above step is finished.

### Start an existed system (from the 2nd time)

Open the docker folder and run below command in the terminal.

- `docker compose start`

## Useful tips

### Start an interactive Bash shell in a Docker container

Run `docker compose exec {serviceName} bash`. For example: `docker compose exec api bash`

### Add a composer package

Run `docker compose exec api composer require {packageName}`. For example: `docker compose exec api composer require phpmd/phpmd`

### Stop server

Run: `docker compose stop`