# Word popularity app

An app that calculates the popularity of a certain word. The app
searches for the GitHub issues using the number of results for `{word} rocks` as a positive and
`{word} sucks` as a negative result. The result should be a popularity rating of the given word from 0-10
as a ratio of positive results to the total number of results.
## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. [Install Docker Desktop](https://www.docker.com/products/docker-desktop/) to easily access docker images
3. Clone project to your local machine and run `docker compose up` (the logs will be displayed in the current shell) in the root folder

## Basic guidelines

There are two versions of the endpoint that differ in the data presented:
* `https://localhost/api/v1/score?term=php`
* `https://localhost/api/v2/score?term=php`

As you can see, 'v1' and 'v2' in the url decide which data will be presented. 
Query part of the word consists of the word 'term' and the word that will be searched on the GitHub issues - in this case 'php'. 

Example: `https://localhost/api/v1/score?term=php`:
```json
{
    "data": {
        "type": "searchTerm",
        "id": "2",
        "attributes": {
            "term": "php",
            "score": "3.33"
        }
    }
}
```

Example: `https://localhost/api/v2/score?term=php`:
```json
{
    "data": {
        "type": "searchTerm",
        "id": "2",
        "attributes": {
            "term": "php",
            "positiveResults": 219,
            "negativeResults": 439
        }
    }
}
```

If the app is unable to recognize the search term, error message is presented to the user.

Example: `https://localhost/api/v2/score?ter`:
```json
{
    "errors": {
        "title": "Search term does not exist."
    }
}
```

## PHPUnit test
There is currently only one test in the app that asserts GitHub API get request.

Run the test by executing command `php bin/phpunit` inside php container.

## OAuth2

There is a basic OAuth2 setup in place upon which user authentication can be built. The default client is Facebook. Check out the following [documentation](https://github.com/knpuniversity/oauth2-client-bundle)
to add user authenticators once the users are introduced in the app.

The following url starts Facebook OAuth2 flow:
`https://localhost/oauth/connect`

In order to access Facebook data of the user that wants to authenticate,
`OAUTH_FACEBOOK_ID` and `OAUTH_FACEBOOK_SECRET` need to be set in the `.env` file. This info should be a secret, so they are not included in this project repository.

## OpenApi 3

There is OpenApi 3 project specification available in the json format on the following url:
`https://localhost/api/doc.json`
