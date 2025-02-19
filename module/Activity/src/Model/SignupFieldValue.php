<?php

declare(strict_types=1);

namespace Activity\Model;

use Application\Model\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * SignupFieldValue model.
 */
#[Entity]
class SignupFieldValue
{
    use IdentifiableTrait;

    /**
     * Field which the value belongs to.
     */
    #[ManyToOne(targetEntity: SignupField::class)]
    #[JoinColumn(
        name: 'field_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    protected SignupField $field;

    /**
     * Signup which the value belongs to.
     */
    #[ManyToOne(
        targetEntity: Signup::class,
        inversedBy: 'fieldValues',
    )]
    #[JoinColumn(
        name: 'signup_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    protected Signup $signup;

    /**
     * The value of the associated field, is not an option.
     */
    #[Column(
        type: 'string',
        nullable: true,
    )]
    protected ?string $value = null;

    /**
     * The option chosen.
     */
    #[ManyToOne(targetEntity: SignupOption::class)]
    #[JoinColumn(
        name: 'option_id',
        referencedColumnName: 'id',
    )]
    protected ?SignupOption $option;

    public function getField(): SignupField
    {
        return $this->field;
    }

    /**
     * Set the field.
     */
    public function setField(SignupField $field): void
    {
        $this->field = $field;
    }

    public function getSignup(): Signup
    {
        return $this->signup;
    }

    /**
     * Set the signup.
     */
    public function setSignup(Signup $signup): void
    {
        $this->signup = $signup;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Set the value.
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getOption(): ?SignupOption
    {
        return $this->option;
    }

    public function setOption(?SignupOption $option): void
    {
        $this->option = $option;
    }
}
