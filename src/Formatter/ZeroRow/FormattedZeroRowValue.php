<?php


namespace App\Formatter\ZeroRow;


class FormattedZeroRowValue
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string|null
     */
    private $source;

    public function __construct(string $value, ?string $source)
    {
        $this->value = $value;
        $this->source = $source;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }
}