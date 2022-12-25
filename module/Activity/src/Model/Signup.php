<?php

namespace Activity\Model;

use Application\Model\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Collection,
};
use Doctrine\ORM\Mapping\{
    DiscriminatorColumn,
    DiscriminatorMap,
    Entity,
    InheritanceType,
    JoinColumn,
    ManyToOne,
    OneToMany,
};

/**
 * Signup model.
 */
#[Entity]
#[InheritanceType(value: "SINGLE_TABLE")]
#[DiscriminatorColumn(
    name: "type",
    type: "string",
)]
#[DiscriminatorMap(
    value: [
        "user" => UserSignup::class,
        "external" => ExternalSignup::class,
    ],
)]
abstract class Signup
{
    use IdentifiableTrait;

    /**
     * The SignupList the signup is for.
     */
    #[ManyToOne(
        targetEntity: SignupList::class,
        inversedBy: "signUps",
    )]
    #[JoinColumn(
        name: "signuplist_id",
        referencedColumnName: "id",
        nullable: false,
    )]
    protected SignupList $signupList;

    /**
     * Additional field values for this Signup.
     */
    #[OneToMany(
        targetEntity: SignupFieldValue::class,
        mappedBy: "signup",
        cascade: ["persist", "remove"],
    )]
    protected Collection $fieldValues;

    public function __construct()
    {
        $this->fieldValues = new ArrayCollection();
    }

    /**
     * Get the SignupList which the user is signed up for.
     *
     * @return SignupList
     */
    public function getSignupList(): SignupList
    {
        return $this->signupList;
    }

    /**
     * Set the SignupList that the user signed up for.
     */
    public function setSignupList(SignupList $signupList): void
    {
        $this->signupList = $signupList;
    }

    /**
     * Get all the extra field values.
     *
     * @return Collection
     */
    public function getFieldValues(): Collection
    {
        return $this->fieldValues;
    }

    /**
     * Get the full name of the user whom signed up for the SignupList.
     *
     * @return string
     */
    abstract public function getFullName(): string;

    /**
     * Get the email address of the user whom signed up for the SignupList.
     *
     * @return string|null
     */
    abstract public function getEmail(): ?string;
}
