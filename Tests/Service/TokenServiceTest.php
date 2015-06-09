<?php

namespace Innobyte\TokenBundle\Tests\Service;

use Innobyte\TokenBundle\Entity\Token;
use Innobyte\TokenBundle\Service\TokenService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TokenServiceTest
 * Used to test the Service and Entity
 *
 * @author Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class TokenServiceTest extends WebTestCase
{
    /**
     * @var TokenService
     */
    protected $tokenService;

    /**
     * Run before each test
     */
    public function setUp()
    {
        $client = static::createClient();

        $this->tokenService = $client->getContainer()->get('innobyte_token');
    }

    /**
     * Test generating a valid Token, with all the fields set
     */
    public function testValidTokenGenerationWithAllFields()
    {
        $scope      = uniqid('scope_');
        $ownerType  = uniqid('owner_');
        $ownerId    = rand(1, 10000);
        $usesMax    = 2;
        $expiryTime = new \DateTime('+1 day');
        $dataKey    = 'additional_key';
        $dataValue  = 'additional_value';
        $data       = array($dataKey => $dataValue);

        $token = $this->tokenService->generate(
            $scope,
            $ownerType,
            $ownerId,
            $usesMax,
            $expiryTime,
            $data
        );

        $currentTime = new \DateTime();

        $this->assertInstanceOf(get_class(new Token()), $token, '$token is not instanceof Token.');
        $this->assertEquals($token->getScope(), $scope, 'Scope is not the same as set');
        $this->assertEquals($token->getOwnerType(), $ownerType, 'Owner type is not the same as set');
        $this->assertEquals($token->getOwnerId(), $ownerId, 'Owner ID is not the same as set');
        $this->assertInstanceOf('\\DateTime', $token->getCreatedAt(), 'Created time is not a DateTime');
        $this->assertGreaterThanOrEqual(
            $currentTime->getTimestamp() - 1,
            $token->getCreatedAt()->getTimestamp(),
            'Created time is not correct'
        );
        $this->assertLessThanOrEqual(
            $currentTime->getTimestamp() + 1,
            $token->getCreatedAt()->getTimestamp(),
            'Created time is not correct'
        );
        $this->assertInstanceOf('\\DateTime', $token->getModifiedAt(), 'Modified time is not a DateTime');
        $this->assertGreaterThanOrEqual(
            $currentTime->getTimestamp() - 1,
            $token->getModifiedAt()->getTimestamp(),
            'Modified time is not correct'
        );
        $this->assertLessThanOrEqual(
            $currentTime->getTimestamp() + 1,
            $token->getModifiedAt()->getTimestamp(),
            'Modified time is not correct'
        );
        $this->assertEquals($token->getUsesMax(), $usesMax, 'Max uses is not the same as set');
        $this->assertInstanceOf('\\DateTime', $token->getExpiresAt(), '$expiresAt is not a DateTime');
        $this->assertEquals($token->getExpiresAt(), $expiryTime, '$expiresAt is not the same as set');
        $this->assertArrayHasKey($dataKey, $token->getData(), 'Data is not the same as set');
        $this->assertEquals($token->getData(), $data, 'Data is not the same as set');

        $hash = $token->getHash();

        $this->assertNotEmpty($hash, 'Hash is empty');
        $this->assertEquals(32, strlen($hash), 'Hash length is not consistent with md5');
        $this->assertGreaterThan(0, $token->getId(), 'Token ID was not populated');

        return $token;
    }

    /**
     * Test generating a valid Token, only with the basic fields set
     */
    public function testValidTokenGenerationWithBasicFields()
    {
        $scope     = uniqid('scope_');
        $ownerType = uniqid('owner_');
        $ownerId   = rand(1, 10000);

        $token = $this->tokenService->generate(
            $scope,
            $ownerType,
            $ownerId
        );

        $currentTime = new \DateTime();

        $this->assertInstanceOf(get_class(new Token()), $token, '$token is not instanceof Token.');
        $this->assertEquals($token->getScope(), $scope, 'Scope is not the same as set');
        $this->assertEquals($token->getOwnerType(), $ownerType, 'Owner type is not the same as set');
        $this->assertEquals($token->getOwnerId(), $ownerId, 'Owner ID is not the same as set');
        $this->assertInstanceOf('\\DateTime', $token->getCreatedAt(), 'Created time is not a DateTime');
        $this->assertGreaterThanOrEqual(
            $currentTime->getTimestamp() - 1,
            $token->getCreatedAt()->getTimestamp(),
            'Created time is not correct'
        );
        $this->assertLessThanOrEqual(
            $currentTime->getTimestamp() + 1,
            $token->getCreatedAt()->getTimestamp(),
            'Created time is not correct'
        );
        $this->assertInstanceOf('\\DateTime', $token->getModifiedAt(), 'Modified time is not a DateTime');
        $this->assertGreaterThanOrEqual(
            $currentTime->getTimestamp() - 1,
            $token->getModifiedAt()->getTimestamp(),
            'Modified time is not correct'
        );
        $this->assertLessThanOrEqual(
            $currentTime->getTimestamp() + 1,
            $token->getModifiedAt()->getTimestamp(),
            'Modified time is not correct'
        );

        $hash = $token->getHash();

        $this->assertNotEmpty($hash, 'Hash is empty');
        $this->assertEquals(32, strlen($hash), 'Hash length is not consistent with md5');
        $this->assertGreaterThan(0, $token->getId(), 'Token ID was not populated');

        return $token;
    }

    /**
     * Test fetching a token
     *
     * @depends testValidTokenGenerationWithAllFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testTokenGetWithAllFields(Token $generatedToken)
    {
        $retrievedToken = $this->tokenService->get(
            $generatedToken->getHash(),
            $generatedToken->getScope(),
            $generatedToken->getOwnerType(),
            $generatedToken->getOwnerId()
        );

        $this->assertInstanceOf(get_class(new Token()), $retrievedToken, '$retrievedToken is not instanceof Token.');
        $this->assertEquals($generatedToken->getScope(), $retrievedToken->getScope(), 'Scope is not the same as set');
        $this->assertEquals(
            $generatedToken->getOwnerType(),
            $retrievedToken->getOwnerType(),
            'Owner type is not the same as set'
        );
        $this->assertEquals(
            $generatedToken->getOwnerId(),
            $retrievedToken->getOwnerId(),
            'Owner ID is not the same as set'
        );
        $this->assertInstanceOf('\\DateTime', $retrievedToken->getCreatedAt(), 'Created time is not a DateTime');
        $this->assertEquals(
            $generatedToken->getCreatedAt()->getTimestamp(),
            $retrievedToken->getCreatedAt()->getTimestamp(),
            'Created time is not correct'
        );
        $this->assertInstanceOf('\\DateTime', $retrievedToken->getModifiedAt(), 'Modified time is not a DateTime');
        $this->assertEquals(
            $generatedToken->getModifiedAt()->getTimestamp(),
            $retrievedToken->getModifiedAt()->getTimestamp(),
            'Modified time is not correct'
        );
        $this->assertEquals(
            $generatedToken->getUsesMax(),
            $retrievedToken->getUsesMax(),
            'Max uses is not the same as set'
        );
        $this->assertInstanceOf('\\DateTime', $retrievedToken->getExpiresAt(), '$expiresAt is not a DateTime');
        $this->assertEquals(
            $generatedToken->getExpiresAt(),
            $retrievedToken->getExpiresAt(),
            '$expiresAt is not the same as set'
        );
        $this->assertEquals($generatedToken->getData(), $retrievedToken->getData(), '$data is not the same as set');
    }

    /**
     * Test fetching a token
     *
     * @depends testValidTokenGenerationWithBasicFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testTokenGetWithBasicFields(Token $generatedToken)
    {
        $retrievedToken = $this->tokenService->get(
            $generatedToken->getHash(),
            $generatedToken->getScope(),
            $generatedToken->getOwnerType(),
            $generatedToken->getOwnerId()
        );

        $this->assertInstanceOf(get_class(new Token()), $retrievedToken, '$retrievedToken is not instanceof Token.');
        $this->assertEquals($generatedToken->getScope(), $retrievedToken->getScope(), 'Scope is not the same as set');
        $this->assertEquals(
            $generatedToken->getOwnerType(),
            $retrievedToken->getOwnerType(),
            'Owner type is not the same as set'
        );
        $this->assertEquals(
            $generatedToken->getOwnerId(),
            $retrievedToken->getOwnerId(),
            'Owner ID is not the same as set'
        );
        $this->assertInstanceOf('\\DateTime', $retrievedToken->getCreatedAt(), 'Created time is not a DateTime');
        $this->assertEquals(
            $generatedToken->getCreatedAt()->getTimestamp(),
            $retrievedToken->getCreatedAt()->getTimestamp(),
            'Created time is not correct'
        );
        $this->assertInstanceOf('\\DateTime', $retrievedToken->getModifiedAt(), 'Modified time is not a DateTime');
        $this->assertEquals(
            $generatedToken->getModifiedAt()->getTimestamp(),
            $retrievedToken->getModifiedAt()->getTimestamp(),
            'Modified time is not correct'
        );
        $this->assertEquals($generatedToken->getUsesMax(), 1, 'Max uses is not the default');
        $this->assertNull($retrievedToken->getExpiresAt(), '$expiresAt is not null');
        $this->assertEmpty($generatedToken->getData(), '$data is not empty');
    }

    /**
     * Test valid token without expiry time set
     *
     * @depends testValidTokenGenerationWithBasicFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testIsValidPassWithoutExpiry(Token $generatedToken)
    {
        $this->assertTrue(
            $this->tokenService->isValid($generatedToken),
            'Expected valid Token without expiry is not valid.'
        );
    }

    /**
     * Test valid token with expiry time set
     *
     * @depends testValidTokenGenerationWithAllFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testIsValidPassWithExpiry(Token $generatedToken)
    {
        $this->assertTrue(
            $this->tokenService->isValid($generatedToken),
            'Expected valid Token with expiry is not valid.'
        );
    }

    /**
     * Test with no Token
     */
    public function testIsValidFailToken()
    {
        $this->assertFalse($this->tokenService->isValid(null), 'No Token passed token validation');
    }

    /**
     * Test valid token without expiry time set
     *
     * @depends testValidTokenGenerationWithBasicFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testIsValidFailInactive(Token $generatedToken)
    {
        $inactiveToken = clone $generatedToken;
        $inactiveToken->setActive(false);

        $this->assertFalse($this->tokenService->isValid($inactiveToken), 'Inactive Token passed validation');
    }

    /**
     * Test valid token without expiry time set
     *
     * @depends testValidTokenGenerationWithBasicFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testIsValidFailOverused(Token $generatedToken)
    {
        $overusedToken = clone $generatedToken;
        $overusedToken->setUsesCount($generatedToken->getUsesMax() + 1);

        $this->assertFalse($this->tokenService->isValid($overusedToken), 'Overused Token passed validation');
    }

    /**
     * Test valid token without expiry time set
     *
     * @depends testValidTokenGenerationWithBasicFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testIsValidFailExpired(Token $generatedToken)
    {
        $expiredToken = clone $generatedToken;
        $expiredToken->setExpiresAt(new \DateTime('5 minutes ago'));

        $this->assertFalse($this->tokenService->isValid($expiredToken), 'Expired Token passed validation');
    }

    /**
     * Test consume Token by criteria
     *
     * @depends testValidTokenGenerationWithBasicFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testConsumePass(Token $generatedToken)
    {
        $beforeUses = $generatedToken->getUsesCount();

        $this->tokenService->consume(
            $generatedToken->getHash(),
            $generatedToken->getScope(),
            $generatedToken->getOwnerType(),
            $generatedToken->getOwnerId()
        );

        $retrievedToken = $this->tokenService->get(
            $generatedToken->getHash(),
            $generatedToken->getScope(),
            $generatedToken->getOwnerType(),
            $generatedToken->getOwnerId()
        );

        $this->assertEquals($beforeUses + 1, $retrievedToken->getUsesCount(), 'Token was not consumed');
    }

    /**
     * Test trying to consume inexistent Token
     *
     * @expectedException \Innobyte\TokenBundle\Exception\TokenNotFoundException
     */
    public function testConsumeFailToken()
    {
        $this->tokenService->consume(
            'inexistent_hash',
            'inexistent_scope',
            'inexistent_owner_type',
            0
        );
    }

    /**
     * Test trying to consume not active Token
     *
     * @expectedException \Innobyte\TokenBundle\Exception\TokenInactiveException
     */
    public function testConsumeFailInactive()
    {
        $scope     = uniqid('scope_');
        $ownerType = uniqid('owner_');
        $ownerId   = rand(1, 10000);

        $token = $this->tokenService->generate(
            $scope,
            $ownerType,
            $ownerId
        );

        $this->tokenService->invalidateToken($token);

        $this->tokenService->consume(
            $token->getHash(),
            $token->getScope(),
            $token->getOwnerType(),
            $token->getOwnerId()
        );
    }

    /**
     * Test trying to consume already consumed Token
     *
     * @depends testValidTokenGenerationWithBasicFields
     *
     * @expectedException \Innobyte\TokenBundle\Exception\TokenConsumedException
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testConsumeFailConsumed(Token $generatedToken)
    {
        // Token was already consumed in previous tests
        $this->tokenService->consume(
            $generatedToken->getHash(),
            $generatedToken->getScope(),
            $generatedToken->getOwnerType(),
            $generatedToken->getOwnerId()
        );
    }

    /**
     * Test trying to consume expired Token
     *
     * @expectedException \Innobyte\TokenBundle\Exception\TokenExpiredException
     */
    public function testConsumeFailExpired()
    {
        $scope      = uniqid('scope_');
        $ownerType  = uniqid('owner_');
        $ownerId    = rand(1, 10000);
        $usesMax    = 1;
        $expiryTime = new \DateTime('5 minutes ago');

        $token = $this->tokenService->generate(
            $scope,
            $ownerType,
            $ownerId,
            $usesMax,
            $expiryTime
        );

        $this->tokenService->consume(
            $token->getHash(),
            $token->getScope(),
            $token->getOwnerType(),
            $token->getOwnerId()
        );
    }

    /**
     * Test consume Token
     */
    public function testConsumeTokenPass()
    {
        $scope     = uniqid('scope_');
        $ownerType = uniqid('owner_');
        $ownerId   = rand(1, 10000);

        $token = $this->tokenService->generate(
            $scope,
            $ownerType,
            $ownerId
        );

        $beforeUses = $token->getUsesCount();

        $this->tokenService->consumeToken($token);

        $this->assertEquals($beforeUses + 1, $token->getUsesCount(), 'Token was not consumed');

        $retrievedToken = $this->tokenService->get(
            $token->getHash(),
            $token->getScope(),
            $token->getOwnerType(),
            $token->getOwnerId()
        );

        $this->assertEquals($beforeUses + 1, $retrievedToken->getUsesCount(), 'Token was not consumed in Database');
    }

    /**
     * Test trying to consume Token not managed by Doctrine
     *
     * @expectedException \LogicException
     */
    public function testConsumeTokenFailUnmanaged()
    {
        $this->tokenService->consumeToken(new Token());
    }

    /**
     * Test invalidate Token by criteria
     *
     * @depends testValidTokenGenerationWithBasicFields
     *
     * @param Token $generatedToken Previously generated Token
     */
    public function testInvalidatePass(Token $generatedToken)
    {
        $this->assertTrue($generatedToken->isActive(), 'Expected active Token was inactive');

        $this->tokenService->invalidate(
            $generatedToken->getHash(),
            $generatedToken->getScope(),
            $generatedToken->getOwnerType(),
            $generatedToken->getOwnerId()
        );

        $retrievedToken = $this->tokenService->get(
            $generatedToken->getHash(),
            $generatedToken->getScope(),
            $generatedToken->getOwnerType(),
            $generatedToken->getOwnerId()
        );

        $this->assertFalse($retrievedToken->isActive(), 'Token was not invalidated');
    }

    /**
     * Test trying to invalidate inexistent Token
     *
     * @expectedException \Innobyte\TokenBundle\Exception\TokenNotFoundException
     */
    public function testInvalidateFailToken()
    {
        $this->tokenService->invalidate(
            'inexistent_hash',
            'inexistent_scope',
            'inexistent_owner_type',
            0
        );
    }

    /**
     * Test consume Token
     */
    public function testInvalidateTokenPass()
    {
        $scope     = uniqid('scope_');
        $ownerType = uniqid('owner_');
        $ownerId   = rand(1, 10000);

        $token = $this->tokenService->generate(
            $scope,
            $ownerType,
            $ownerId
        );

        $this->assertTrue($token->isActive(), 'Expected active Token was inactive');

        $this->tokenService->invalidateToken($token);

        $this->assertFalse($token->isActive(), 'Token was not invalidated');

        $retrievedToken = $this->tokenService->get(
            $token->getHash(),
            $token->getScope(),
            $token->getOwnerType(),
            $token->getOwnerId()
        );

        $this->assertFalse($retrievedToken->isActive(), 'Token was not invalidated in Database');
    }

    /**
     * Test trying to invalidate Token not managed by Doctrine
     *
     * @expectedException \LogicException
     */
    public function testInvalidateTokenFailUnmanaged()
    {
        $this->tokenService->invalidateToken(new Token());
    }
}
