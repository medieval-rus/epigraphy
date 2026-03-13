<?php

declare(strict_types=1);

namespace App\Persistence\Entity\Epigraphy;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="localized_text",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(
 *             name="localized_text_unique_target_field_locale",
 *             columns={"target_type", "target_id", "field", "locale"}
 *         )
 *     },
 *     indexes={
 *         @ORM\Index(name="localized_text_target_idx", columns={"target_type", "target_id"}),
 *         @ORM\Index(name="localized_text_locale_idx", columns={"locale"}),
 *         @ORM\Index(name="localized_text_field_idx", columns={"field"})
 *     }
 * )
 */
class LocalizedText
{
    public const TARGET_INSCRIPTION = 'inscription';
    public const TARGET_CARRIER = 'carrier';
    public const TARGET_ZERO_ROW = 'zero_row';
    public const TARGET_INTERPRETATION = 'interpretation';

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="target_type", type="string", length=32)
     */
    private $targetType;

    /**
     * @var int
     *
     * @ORM\Column(name="target_id", type="integer")
     */
    private $targetId;

    /**
     * @var string
     *
     * @ORM\Column(name="field", type="string", length=64)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=false)
     */
    private $value;

    public static function resolveTargetTypeFromEntity($entity): ?string
    {
        if ($entity instanceof Inscription) {
            return self::TARGET_INSCRIPTION;
        }
        if ($entity instanceof Carrier) {
            return self::TARGET_CARRIER;
        }
        if ($entity instanceof ZeroRow) {
            return self::TARGET_ZERO_ROW;
        }
        if ($entity instanceof Interpretation) {
            return self::TARGET_INTERPRETATION;
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function setTargetType(string $targetType): self
    {
        $this->targetType = $targetType;
        return $this;
    }

    public function getTargetId(): ?int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): self
    {
        $this->targetId = $targetId;
        return $this;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(string $field): self
    {
        $this->field = $field;
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = strtolower($locale);
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
}
