# Installation

## composer.json - install bundle

    "repositories": [
        {
            "type": "vcs",
            "url": "ssh://git@gitlab.devops.innobyte.ro/sorin.dumitrescu/token-bundle.git"
        }
    ],
    "require": {
        "innobyte/token-bundle": "@dev",
    },

## AppKernel.php - register bundle

Add the following line into `$bundles` array:

    new Innobyte\TokenBundle\InnobyteTokenBundle(),

## config.yml - Add bundle mapping

Add the mapping for the bundle under an entity manager (here "default")

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        InnobyteTokenBundle: ~

## paramaters.yml - Add the entity manager name

Add the entity manager name (here "local") - the one you put the mapping under in the above step.
If none is provided, "default" will be used.

    innobyte_token:
        entity_manager: local


# Usage

## Generate token

    /** @var Token $token */
    $token = $this->get('innobyte_token')->generate(
        'scope',
        'owner_type',
        123            // owner_id
    );

## Validate token and consume it
    $isValidToken = $this->get('innobyte_token')->consume(
        '5c15e262c692dbaac75451dcb28282ab',
        'scope',
        'owner_type',
        123            // owner_id
    );

    if (!$isValidToken) {
        echo 'handle invalid token here';
    } else {
        echo 'do stuff here...';
    }

## Get the token and manually validate it
    $token = $this->get('innobyte_token')->get(
        '5c15e262c692dbaac75451dcb28282ab',
        'scope',
        'owner_type',
        123            // owner_id
    );

    // if wrong link or disabled/overused token
    if (!$this->get('innobyte_token')->isValid($token)) {
        echo 'handle invalid token here';
    }

    // or even more explicit

    if (!($token instanceof \Innobyte\TokenBundle\Entity\Token)) {
        echo 'handle invalid token here';
    } else {
        if (!$token->isActive()) {
            echo 'handle explicit disabled token here';
        }

        if ($token->getUsesCount() >= $token->getUsesMax()) {
            echo 'handle explicit over-used token here';
        }

        // manually mark the usage
        $this->get('innobyte_token')->consumeToken($token);

        echo 'do stuff here...';
    }

## Invalidate
Similar methods to "consume" are available for invalidation: "invalidate" (disable the token)

    $this->get('innobyte_token')->invalidate(
            '5c15e262c692dbaac75451dcb28282ab',
            'scope',
            'owner_type',
            123            // owner_id
        );

    $this->get('innobyte_token')->invalidateToken($token);
