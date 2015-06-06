<?php

namespace Innobyte\TokenBundle\Service;

use Doctrine\ORM\UnitOfWork;
use Innobyte\TokenBundle\Exception\TokenConsumedException;
use Innobyte\TokenBundle\Exception\TokenInactiveException;
use Innobyte\TokenBundle\Exception\TokenNotFoundException;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Innobyte\TokenBundle\Entity\Token;

/**
 * Class TokenService
 * Token Manager class
 *
 * @package Innobyte\TokenBundle\Service
 *
 * @author  Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class TokenService
{
    /** @var EntityManager */
    protected $em;

    /**
     * Initialize
     *
     * @param Container $container
     * @param string    $emName
     */
    public function __construct(Container $container, $emName)
    {
        $this->em = $container->get($emName);
    }

    /**
     * Get the entity manager
     *
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->em;
    }

    /**
     * Generate a new token and persist it into DB
     *
     * @param string       $scope
     * @param string       $ownerType
     * @param integer      $ownerId
     * @param integer      $usesMax
     * @param \DateTime    $expiresAt
     * @param object|array $data
     *
     * @return Token
     */
    public function generate($scope, $ownerType, $ownerId, $usesMax = 1, \DateTime $expiresAt = null, $data = null)
    {
        $hash = md5(uniqid() . $scope . $ownerType . $ownerId);

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

        $this->getEm()->persist($token);
        $this->getEm()->flush($token);

        return $token;
    }

    /**
     * Retrieve the Token by given criteria
     *
     * @param string  $hash
     * @param string  $scope
     * @param string  $ownerType
     * @param integer $ownerId
     *
     * @return Token|null
     */
    public function get($hash, $scope, $ownerType, $ownerId)
    {
        $token = $this->getEm()->getRepository('InnobyteTokenBundle:Token')->findOneBy(
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
        return ($token instanceof Token) && $token->isActive() && ($token->getUsesCount() < $token->getUsesMax());
    }

    /**
     * Increase use count for the token matching the given criteria (if it exists and is valid)
     *
     * @param string  $hash
     * @param string  $scope
     * @param string  $ownerType
     * @param integer $ownerId
     *
     * @throws TokenNotFoundException When the Token is not found by hash
     * @throws TokenInactiveException When the Token is not active
     * @throws TokenConsumedException When the Token is already consumed
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

        $this->consumeToken($token);
    }

    /**
     * Consume the given Token
     *
     * @param Token $token
     *
     * @throws \LogicException When a not managed Token is being consumed
     */
    public function consumeToken(Token $token)
    {
        if ($this->getEm()->getUnitOfWork()->getEntityState($token) != UnitOfWork::STATE_MANAGED) {
            throw new \LogicException('The Token must be managed by Doctrine in order to be consumed.');
        }

        $token->setUsesCount(
            $token->getUsesCount() + 1
        );

        $this->getEm()->flush($token);
    }

    /**
     * Invalidate the token matching the given criteria
     *
     * @param string  $hash
     * @param string  $scope
     * @param string  $ownerType
     * @param integer $ownerId
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
     * Invalidates the given Token
     *
     * @param Token $token
     *
     * @throws \LogicException When a not managed Token is being consumed
     */
    public function invalidateToken(Token $token)
    {
        if ($this->getEm()->getUnitOfWork()->getEntityState($token) != UnitOfWork::STATE_MANAGED) {
            throw new \LogicException('The Token must be managed by Doctrine in order to be consumed.');
        }

        $token->setActive(false);

        $this->getEm()->flush($token);
    }
}
