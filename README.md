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
        ...
        orm:
            ...
            entity_managers:
                default:
                    ...
                    mappings:
                        ...
                        InnobyteTokenBundle: ~

## paramaters.yml - Add the entity manager name

Add the entity manager name (here "local") - the one you put the mapping under in the above step.
If none is provided, "default" will be used.

    innobyte_token:
        entity_manager: default


# Usage

## Generate token

    /** @var \Innobyte\TokenBundle\Exception\Token $token */
    $token = $this->get('innobyte_token')->generate(
        'scope',
        'owner_type',
        123            // owner_id
    );

## Validate token and consume it
    try {
        $this->get('innobyte_token')->consume(
            '19debf971fb937853d77fca8fd3bb775',
            'scope',
            'owner_type',
            123            // owner_id
        );
    } catch (\Innobyte\TokenBundle\Exception\TokenNotFoundException $e) {
        echo 'Handle Token not found here';
    } catch (\Innobyte\TokenBundle\Exception\TokenInactiveException $e) {
        echo 'Handle explicit disabled token here';
    } catch (\Innobyte\TokenBundle\Exception\TokenConsumedException $e) {
        echo 'Handle explicit over-used token here';
    }

    echo 'Token is valid. Token is valid. Perform logic here.';

## Get the token and manually validate it
    $token = $this->get('innobyte_token')->get(
        '5c15e262c692dbaac75451dcb28282ab',
        'scope',
        'owner_type',
        123            // owner_id
    );

    // if wrong link or disabled/overused token
    if (!$token instanceof \Innobyte\TokenBundle\Entity\Token || !$this->get('innobyte_token')->isValid($token)) {
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
            echo 'Cannot consume Token because it is not managed';
        } catch (\Innobyte\TokenBundle\Exception\TokenInactiveException $e) {
            echo 'Handle explicit disabled token here';
        } catch (\Innobyte\TokenBundle\Exception\TokenConsumedException $e) {
            echo 'Handle explicit over-used token here';
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
