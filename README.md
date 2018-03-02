# restreamer-proxy

Re-streaming multimedia content from files hosting.
How it works? Give it a link to a page with video, it will re-stream it to you or redirect to a direct link.

Load balances the content, if one hosting fails or removes a video, then a next hosting is served.

## Handlers

List of supported files hosting sites:
- cda.pl
- ... submit your PR and maintain an implementation ...

#### Technical specification

- Built on Symfony 4 and Doctrine ORM
- Uses HttpFoundation request/responses to stream the content
- It's a tiny API application
- Does not contain authorization, needs to be hidden behind a good nginx/apache/other webserver configuration
