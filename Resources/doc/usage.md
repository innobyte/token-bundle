# Generate token

    /** @var Token $token */
    $token = $this->get('innobyte_token')->generate(
        'scope',
        'owner_type',
        123            // owner_id
    );

# Validate token and consume it
    $validToken = $this->get('innobyte_token')->consume(
        '5c15e262c692dbaac75451dcb28282ab',
        'scope',
        'owner_type',
        123            // owner_id
    );

    if (!$validToken) {
        echo 'handle invalid token here';
    } else {
        echo 'do stuff here...';
    }

# Get the token and manually validate it
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
