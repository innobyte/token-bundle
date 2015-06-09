# Purpose

The goal of TokenBundle is to provide a means to validate actions taken by users.

The validation is achieved by using a hash, which could, for example, be embedded in a URL, be it in an email or a link generated on-the-spot inside a page.

# Installation

## composer.json - install bundle

    "repositories": [
        {
            "type": "vcs",
            "url": "ssh://git@gitlab.devops.innobyte.ro/sorin.dumitrescu/token-bundle.git"
        }
    ],
    "require": {
        "innobyte/token-bundle": "~1.0",
    },

## AppKernel.php - register bundle

Add the following line into `$bundles` array:

    new Innobyte\TokenBundle\InnobyteTokenBundle(),

## config.yml - Add bundle mapping

Add the mapping for the bundle under an entity manager (here "local").
Note: "default" is the default name for the Entity Manager, if not specified otherwise.

    doctrine:
        ...
        orm:
            ...
            entity_managers:
                local:
                    ...
                    mappings:
                        ...
                        InnobyteTokenBundle: ~

## paramaters.yml - Add the entity manager name

Add the entity manager name (here "local") - the one you put the mapping under in the above step.
If none is provided, "default" will be used.
Note: this must be specified only if the entity manager name is different than "default". Otherwise, this step can be omitted entirely.

    innobyte_token:
        entity_manager: local


## Update schema

First, run ```app/console doctrine:schema:update --em=default --dump-sql``` to see the generated SQL.

Then, run ```app/console doctrine:schema:update --em=default --force``` to run the query on the Database.

# Usage

## Generate token

    $token = $this->get('innobyte_token')->generate(
        'scope',
        'owner_type',
        123,                       // owner_id
        1,                         // number of uses - optional
        new \DateTime('+1 day'),   // expiry time - optional
        array('author' => 'admin') // additional data to check against - optional
    );

    // use hash (embed in a link/email etc.)
    $hash = $token->getHash();

## Validate token and consume it
    try {
        $this->get('innobyte_token')->consume(
            '19debf971fb937853d77fca8fd3bb775',
            'scope',
            'owner_type',
            123            // owner_id
        );
    } catch (\Innobyte\TokenBundle\Exception\TokenNotFoundException $e) {
        echo 'cannot find Token';
    } catch (\Innobyte\TokenBundle\Exception\TokenInactiveException $e) {
        echo 'handle explicit disabled token here';
    } catch (\Innobyte\TokenBundle\Exception\TokenConsumedException $e) {
        echo 'handle over-used token here';
    } catch (\Innobyte\TokenBundle\Exception\TokenExpiredException $e) {
        echo 'handle expired token here';
    }

    echo 'Token is valid. Token is valid. Perform logic here.';

## Get the token and manually validate and consume it
    $token = $this->get('innobyte_token')->get(
        '5c15e262c692dbaac75451dcb28282ab',
        'scope',
        'owner_type',
        123            // owner_id
    );

    // if wrong link or disabled/overused token
    if (!$this->get('innobyte_token')->isValid($token)) {
        echo 'Handle invalid token here';
    }

    // or even more explicit
    if (!$token instanceof \Innobyte\TokenBundle\Entity\Token) {
        echo 'handle invalid token here';
    } else {
        // manually mark the usage
        try {
            $this->get('innobyte_token')->consumeToken($token);
        } catch (\LogicException $e) {
            echo 'cannot consumed Token because it is not managed';
        }

        echo 'Token is valid. Perform logic here.';
    }

## Invalidate
Similar methods to "consume" are available for invalidation: "invalidate" (disable the token)

    try {
        $this->get('innobyte_token')->invalidate(
            '5c15e262c692dbaac75451dcb28282ab',
            'scope',
            'owner_type',
            123            // owner_id
        );
    } catch (\Innobyte\TokenBundle\Exception\TokenNotFoundException $e) {
        echo 'Handle Token not found here';
    }

    if (!$token instanceof \Innobyte\TokenBundle\Entity\Token) {
        echo 'Handle invalid token here';
    } else {
        try {
            $this->get('innobyte_token')->invalidateToken($token);
        } catch (\LogicException $e) {
            echo 'Cannot consume Token because it is not managed';
        }
    }

## Advanced validation
Additional data can be used in validating additional conditions after performing the standard Token validation.

    // first, generate the Token
    $token = $this->get('innobyte_token')->generate(
        'scope',
        'owner_type',
        123,                       // owner_id
        1,                         // number of uses - optional
        new \DateTime('+1 hour'),   // expiry time - optional
        array('ip' => $request->getClientIp()) // additional data to check against - optional
    );

    // use hash (embed in a link/email etc.)
    $hash = $token->getHash();
    
    // then, validate it
    $token = $this->get('innobyte_token')->get(
        $hash,
        'scope',
        'owner_type',
        123            // owner_id
    );

    // if wrong link or disabled/overused token
    if (!$this->get('innobyte_token')->isValid($token)) {
        echo 'Handle invalid token here';
    }
    
    // additional validation by IP
    $additionalData = $token->getData();
    if ($additionalData['ip'] != $request->getClientIp()) {
        echo 'Handle invalid token here';
    }

## Unit Testing
Do not forget to override database credentials in ```config_test.yml``` with the test database ones. Example:

    parameters:
        database_host: 127.0.0.1
        database_port: null
        database_name: test_db
        database_user: root
        database_password: 123

To run the tests, execute:

    phpunit -c app/ vendor/innobyte/token-bundle/Innobyte/TokenBundle/Tests/
