# Context

## Project
This is my personal website for my freelancing activity: presentation, portfolio, blog and contacts.

## Dev Environment
- IDE: VSCode
- Host Machine: Docker container running custom image, see docker/Dockerfile.
- Same image of production, plus some customization made in a dedicated if block in the docker/Dockerfile.
- Runtime:  Supervisor managed processes for: nginx, php-fpm, cron, queues, vite watch build (dev only)
- All logs in storage/logs

## Production Environment
- Same Dockerfile (nginx, php-fpm, cron, queues): see docker/Dockerfile
- Served by traefik
- Custom docker-compose.yml, similar to docker-compose.local.yml, without mailpit but with watchtower
- CI/CD by Github Actions, see .github/workflows/deploy.yml
- After watchtower updates the container, by pulling the image from dockerhub, it triggers the /post-update.sh script (locally located in docker/scripts//post-update.sh)
- Opcache enabled (no CLI)
- All logs in storage/logs
