<?php

namespace Innobyte\TokenBundle\Service;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnitOfWork;
use Innobyte\TokenBundle\Exception\TokenConsumedException;
use Innobyte\TokenBundle\Exception\TokenExpiredException;
use Innobyte\TokenBundle\Exception\TokenInactiveException;
use Innobyte\TokenBundle\Exception\TokenNotFoundException;
use Doctrine\ORM\EntityManager;
use Innobyte\TokenBundle\Entity\Token;

/**
 * Class TokenService
 * Token Manager class
 *
 * @package Innobyte\TokenBundle\Service
 *
 * @author Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class TokenService
{
    const ENTITY_NAME = 'InnobyteTokenBundle:Token';

    /**
     * @var EntityManager
     */
    protected $em;

    /** @var EntityRepository */
    protected $repository;

    /**
     * Initialize
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->repository = $this->em->getRepository(self::ENTITY_NAME);
    }

    /**
     * Generate a new token and persist it into DB
     *
     * @param string    $scope     Purpose of this token (where will it be used)
     * @param string    $ownerType Owner of this entity (could be an Entity class name)
     * @param integer   $ownerId   Identifier of the owner
     * @param integer   $usesMax   Maximum number of uses for this Token before it is invalidated
     * @param \DateTime $expiresAt Optional expiry time, after which the Token becomes invalid
     * @param array     $data      Optional additional data to check against, for advanced validation conditions
     *
     * @return Token
     */
    public function generate($scope, $ownerType, $ownerId, $usesMax = 1, \DateTime $expiresAt = null, array $data = null)
    {
        $hash = md5($this->legacyRandomBytes(32) . $scope . $ownerType . $ownerId);

        $token = new Token();
        $token->setHash($hash)
            ->setScope($scope)
            ->setOwnerType($ownerType)
            ->setOwnerId($ownerId)
            ->setUsesMax($usesMax);

        if (!empty($data)) {
            $token->setData($data);
        }

        if ($expiresAt instanceof \DateTime) {
            $token->setExpiresAt($expiresAt);
        }

        $this->em->persist($token);
        $this->em->flush($token);

        return $token;
    }

    /**
     * Retrieve the Token by given criteria
     * Hash is used to find the Token
     * The rest of the criteria is an embedded validation (filtering criteria)
     *
     * @param string  $hash      Unique hash by which to retrieve the Token
     * @param string  $scope     Purpose of this token (where will it be used)
     * @param string  $ownerType Owner of this entity (could be an Entity class name)
     * @param integer $ownerId   Identifier of the owner
     *
     * @return Token|null
     */
    public function get($hash, $scope, $ownerType, $ownerId)
    {
        $token = $this->repository->findOneBy(
            array(
                'hash'      => $hash,
                'scope'     => $scope,
                'ownerType' => $ownerType,
                'ownerId'   => $ownerId,
            )
        );

        return $token;
    }

    /**
     * Check if the token can be used
     *
     * @param Token|null $token
     *
     * @return boolean
     */
    public function isValid(Token $token = null)
    {
        if (!($token instanceof Token)) {
            return false;
        }

        $currentTime = new \DateTime();
        $expiryTime  = $token->getExpiresAt();

        return $token->isActive()
            && ($token->getUsesCount() < $token->getUsesMax())
            && (!($expiryTime instanceof \DateTime) || $expiryTime->getTimestamp() >= $currentTime->getTimestamp());
    }

    /**
     * Increase use count for the Token matching the given criteria (if it exists and is valid)
     * Does not check if the Token can be consumed or not - this check must be performed before calling consume()
     *
     * @param string  $hash      Unique hash by which to retrieve the Token
     * @param string  $scope     Purpose of this token (where will it be used)
     * @param string  $ownerType Owner of this entity (could be an Entity class name)
     * @param integer $ownerId   Identifier of the owner
     *
     * @throws TokenNotFoundException When the Token is not found by hash
     * @throws TokenInactiveException When the Token is not active
     * @throws TokenConsumedException When the Token is already consumed
     * @throws TokenExpiredException  When the Token is expired
     */
    public function consume($hash, $scope, $ownerType, $ownerId)
    {
        $token = $this->get($hash, $scope, $ownerType, $ownerId);

        if (!$token instanceof Token) {
            throw new TokenNotFoundException(sprintf('A Token was not found for hash "%s".', $hash));
        }

        if (!$token->isActive()) {
            throw new TokenInactiveException(sprintf('Token "%s" is not active.', $hash));
        }

        if ($token->getUsesCount() >= $token->getUsesMax()) {
            throw new TokenConsumedException(sprintf('Token "%s" is already consumed.', $hash));
        }

        if (($expiryTime = $token->getExpiresAt()) instanceof \DateTime) {
            $currentTime = new \DateTime();
            if ($expiryTime->getTimestamp() < $currentTime->getTimestamp()) {
                throw new TokenExpiredException(sprintf('Token "%s" is expired.', $hash));
            }
        // @codeCoverageIgnoreStart
        }
        // @codeCoverageIgnoreEnd

        $this->consumeToken($token);
    }

    /**
     * Increase use count for the given Token
     * Does not check if the Token can be consumed or not - this check must be performed before calling consume()
     *
     * @param Token $token The Token entity
     *
     * @throws \LogicException When a not managed Token is being consumed
     */
    public function consumeToken(Token $token)
    {
        if ($this->em->getUnitOfWork()->getEntityState($token) != UnitOfWork::STATE_MANAGED) {
            throw new \LogicException('The Token must be managed by Doctrine in order to be consumed.');
        }

        $token->setUsesCount(
            $token->getUsesCount() + 1
        );

        $this->em->flush($token);
    }

    /**
     * Invalidate the Token matching the given criteria
     * Hash is used to find the Token
     * The rest of the criteria is an embedded validation (filtering criteria)
     *
     * @param string  $hash      Unique hash by which to retrieve the Token
     * @param string  $scope     Purpose of this token (where will it be used)
     * @param string  $ownerType Owner of this entity (could be an Entity class name)
     * @param integer $ownerId   Identifier of the owner
     *
     * @throws TokenNotFoundException When the Token is not found by hash
     */
    public function invalidate($hash, $scope, $ownerType, $ownerId)
    {
        $token = $this->get($hash, $scope, $ownerType, $ownerId);

        if (!$token instanceof Token) {
            throw new TokenNotFoundException(sprintf('A Token was not found for hash "%s".', $hash));
        }

        $this->invalidateToken($token);
    }

    /**
     * Invalidate the given Token
     *
     * @param Token $token The Token entity
     *
     * @throws \LogicException When a not managed Token is being consumed
     */
    public function invalidateToken(Token $token)
    {
        if ($this->em->getUnitOfWork()->getEntityState($token) != UnitOfWork::STATE_MANAGED) {
            throw new \LogicException('The Token must be managed by Doctrine in order to be consumed.');
        }

        $token->setActive(false);

        $this->em->flush($token);
    }

    /**
     * Legacy method for the PHP7 random_bytes() function
     *
     * @param int $length
     *
     * @return string
     */
    protected function legacyRandomBytes($length = 32)
    {
        if (function_exists('random_bytes')) {
            return random_bytes($length);
        }
        if (function_exists('mcrypt_create_iv')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length);
        }

        return $this->dummyRandomBytes($length);
    }

    /**
     * Dummy method for the PHP7 random_bytes() function
     * Will be used in case you don't have PHP >= 7 installed nor the mcrypt library nor the openssl library.
     * Warning: It is not a cryptographically secure pseudorandom string generator!
     *
     * @param int $length
     *
     * @return string
     */
    protected function dummyRandomBytes($length = 32)
    {
        $alphabet = 'abcde1234567890';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 2 * $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return hex2bin(implode($pass));
    }
}
