<?php

declare(strict_types=1);

/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

namespace App\Services\Corpus\Morphy\Models\Xhtml;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class XhtmlWord
{
    public $ana;

    function setLine(XhtmlLine $line) {
        return;
    }

    function __toString() {
        return (string)$this->ana;
    }
}

class XhtmlLine
{
    /**
     * @var ArrayCollection|XhtmlWord[]
     * 
     * @ORM\OneToMany(targetEntity="XhtmlWord", mappedBy="XhtmlLine", orphanRemoval=false)
     * @Groups("Include")
     */
    public $w;

    public function __construct()
    {
        $this->w = new ArrayCollection();
    }
}

class XhtmlDocument
{
    public function __construct()
    {
        $this->page = new ArrayCollection();
    }

    /**
     * @var ArrayCollection|XhtmlLine[]
     */
    public $page;
}

class XhtmlFile
{
    public $head;
    /**
     * @var ArrayCollection|XhtmlDocument[]
     */
    public $body;

    public function __construct()
    {
        $this->body = new ArrayCollection();
    }
}
