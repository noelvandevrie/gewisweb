<?php

declare(strict_types=1);

namespace Activity\Model;

use Application\Model\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * SignupOption model.
 * Contains the possible options of a field of type ``option''.
 */
#[Entity]
class SignupOption
{
    use IdentifiableTrait;

    /**
     * Field that the option belongs to.
     */
    #[ManyToOne(
        targetEntity: SignupField::class,
        cascade: ['persist'],
        inversedBy: 'options',
    )]
    #[JoinColumn(
        name: 'field_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    protected SignupField $field;

    /**
     * The value of the option.
     */
    #[OneToOne(
        targetEntity: ActivityLocalisedText::class,
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    #[JoinColumn(
        name: 'value_id',
        referencedColumnName: 'id',
        nullable: false,
    )]
    protected ActivityLocalisedText $value;

    public function getField(): SignupField
    {
        return $this->field;
    }

    /**
     * Set the field the option belongs to.
     */
    public function setField(SignupField $field): void
    {
        $this->field = $field;
    }

    public function getValue(): ActivityLocalisedText
    {
        return $this->value;
    }

    /**
     * Set the value of the option.
     */
    public function setValue(ActivityLocalisedText $value): void
    {
        $this->value = $value;
    }

    /**
     * Returns an associative array representation of this object.
     *
     * @return array{
     *     id: int,
     *     value: ?string,
     *     valueEn: ?string,
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'value' => $this->getValue()->getValueNL(),
            'valueEn' => $this->getValue()->getValueEN(),
        ];
    }
}
