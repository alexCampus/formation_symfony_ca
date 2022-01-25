<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    private const ROLES = [
        'edit'   => 'POST_EDIT',
        'delete' => 'POST_DELETE'
    ];

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, self::ROLES)
               && $subject instanceof \App\Entity\Post;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Post $post */
        $post = $subject;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ROLES['delete']:
                return $this->canDelete($post, $user);
                break;
            case self::ROLES['edit']:
                return $this->canEdit($post, $user);
                break;
        }

        return false;
    }

    private function canEdit(Post $post, User $user): bool
    {
        return $post->getUser() === $user;
    }

    private function canDelete(Post $post, User $user): bool
    {
        if ($this->canEdit($post, $user)) {
            return true;
        }

        return false;
    }
}
