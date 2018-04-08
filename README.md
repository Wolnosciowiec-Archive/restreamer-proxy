# restreamer-proxy

Re-streaming multimedia content from files hosting.
How it works? Give it a link to a page with video, it will re-stream it to you or redirect to a direct link.

## Library
Library is a group of links that provides the same content but from various sources.
It allows to looad balance the content, if one hosting fails or removes a video, then a next link from eg. next hosting is served.

## Setup

1. Do the `composer install`
2. Adjust the `.env` file eg. database settings 
3. Migrate the database `./bin/console doctrine:migrations:migrate -vv`
4. For testing/development set up a developer server using `./bin/console server:start`

## Example usage

See [postman.json](/postman.json) for example endpoints usage.

## Handlers

List of supported files hosting sites:
- [cda.pl](src/ResourceHandler/Handlers/CdaPLHandler.php)
- ... [submit a PR and maintain an implementation](src/ResourceHandler/Handlers/StreamedHandler.php) ...

#### Technical specification

- Built on Symfony 4 and Doctrine ORM
- Uses HttpFoundation request/responses to stream the content
- It's a tiny API application
- Does not contain authorization, needs to be hidden behind a good nginx/apache/other webserver configuration
