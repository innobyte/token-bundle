<?php

namespace Innobyte\TokenBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Innobyte\TokenBundle\Entity\Token;

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

        if (!empty($expiresAt)) {
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
     * @param string $hash
     * @param string  $scope
     * @param string  $ownerType
     * @param integer $ownerId
     *
     * @return boolean
     */
    public function consume($hash, $scope, $ownerType, $ownerId)
    {
        $token = $this->get($hash, $scope, $ownerType, $ownerId);

        if (!($token instanceof Token)) {
            return false;
        }

        if (!$token->isActive() || $token->getUsesCount() >= $token->getUsesMax()) {
            return false;
        }

        $this->consumeToken($token);

        return true;
    }

    /**
     * Consume the given Token
     *
     * @param Token $token
     */
    public function consumeToken(Token $token)
    {
        $token->setUsesCount(
            $token->getUsesCount() + 1
        );

        $this->getEm()->flush($token);
    }

    /**
     * Invalidate the token matching the given criteria
     *
     * @param string $hash
     * @param string  $scope
     * @param string  $ownerType
     * @param integer $ownerId
     */
    public function invalidate($hash, $scope, $ownerType, $ownerId)
    {
        $token = $this->get($hash, $scope, $ownerType, $ownerId);

        $this->invalidateToken($token);
    }

    /**
     * Invalidates the given Token
     *
     * @param Token $token
     */
    public function invalidateToken(Token $token)
    {
        $token->setActive(false);

        $this->getEm()->flush($token);
    }
}
