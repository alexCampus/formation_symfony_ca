<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Post;
use ErrorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LikeDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{

    private $tokenStorage;
    private $logger;

    public function __construct(TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->tokenStorage = $tokenStorage;
        $this->logger       = $logger;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Post::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $token = $this->tokenStorage->getToken();
        $user  = null;
        if ($token === null) {
            $this->logger->error('pas de token');
        } else {
            $user = $token->getUser();
        }


        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $this->logger->error('pas de user');
        }
        $data = [];
        if (isset($user)) {
            foreach ($user->getPostLikes() as $key => $post) {
                $data[$key]['name']    = $post->getName();
                $data[$key]['content'] = $post->getContent();
                $data[$key]['tag']     = $post->getTags();
                $data[$key]['user']    = $post->getUser();
            }
        }


        return $data;
    }


}