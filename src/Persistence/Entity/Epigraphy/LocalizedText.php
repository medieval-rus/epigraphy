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
    public const TARGET_CONTENT_CATEGORY = 'content_category';
    public const TARGET_WRITING_METHOD = 'writing_method';
    public const TARGET_ALPHABET = 'alphabet';
    public const TARGET_MATERIAL = 'material';
    public const TARGET_DISCOVERY_SITE = 'discovery_site';
    public const TARGET_CITY = 'city';
    public const TARGET_COUNTRY = 'country';
    public const TARGET_RIVER = 'river';
    public const TARGET_RIVER_TYPE = 'river_type';
    public const TARGET_STORAGE_SITE = 'storage_site';

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

    /**
     * @var bool
     *
     * @ORM\Column(name="is_ai_generated", type="boolean", options={"default" : false})
     */
    private $isAiGenerated = false;

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
        if ($entity instanceof ContentCategory) {
            return self::TARGET_CONTENT_CATEGORY;
        }
        if ($entity instanceof WritingMethod) {
            return self::TARGET_WRITING_METHOD;
        }
        if ($entity instanceof Alphabet) {
            return self::TARGET_ALPHABET;
        }
        if ($entity instanceof Material) {
            return self::TARGET_MATERIAL;
        }
        if ($entity instanceof DiscoverySite) {
            return self::TARGET_DISCOVERY_SITE;
        }
        if ($entity instanceof City) {
            return self::TARGET_CITY;
        }
        if ($entity instanceof Country) {
            return self::TARGET_COUNTRY;
        }
        if ($entity instanceof River) {
            return self::TARGET_RIVER;
        }
        if ($entity instanceof RiverType) {
            return self::TARGET_RIVER_TYPE;
        }
        if ($entity instanceof StorageSite) {
            return self::TARGET_STORAGE_SITE;
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

    public function isAiGenerated(): bool
    {
        return (bool) $this->isAiGenerated;
    }

    public function setIsAiGenerated(bool $isAiGenerated): self
    {
        $this->isAiGenerated = $isAiGenerated;

        return $this;
    }
}
