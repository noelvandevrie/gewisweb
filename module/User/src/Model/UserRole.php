<?php

declare(strict_types=1);

namespace User\Model;

use Application\Model\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use User\Model\Enums\UserRoles;

/**
 * User role model.
 *
 * This specifies all the roles of a user.
 */
#[Entity]
class UserRole
{
    use IdentifiableTrait;

    /**
     * The membership number of the user with this role.
     */
    #[ManyToOne(
        targetEntity: User::class,
        inversedBy: 'roles',
    )]
    #[JoinColumn(
        referencedColumnName: 'lidnr',
        nullable: false,
    )]
    protected User $lidnr;

    /**
     * The user's role.
     */
    #[Column(
        type: 'string',
        enumType: UserRoles::class,
    )]
    protected UserRoles $role;

    /**
     * Get the membership number.
     */
    public function getLidnr(): User
    {
        return $this->lidnr;
    }

    /**
     * Set the membership number.
     */
    public function setLidnr(User $lidnr): void
    {
        $this->lidnr = $lidnr;
    }

    /**
     * Get the role.
     */
    public function getRole(): UserRoles
    {
        return $this->role;
    }

    /**
     * Set the role.
     */
    public function setRole(UserRoles $role): void
    {
        $this->role = $role;
    }
}
