<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sort_record')]
class SortRecord
{
    #[ORM\Id, ORM\Column(type: 'integer'), ORM\GeneratedValue]
    public ?int $id = null;

    #[ORM\Column(type: 'string')]
    public string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }
}
