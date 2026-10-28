<?php

declare(strict_types=1);

/*
 * This file is part of VyfonyFilterableTableBundle project.
 *
 * (c) Anton Dyshkant <vyshkant@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vyfony\Bundle\FilterableTableBundle\Table;

use Symfony\Component\Form\FormView;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\TableMetadataInterface;

interface TableInterface
{
    public function getFormView(): FormView;

    public function getTableMetadata(): TableMetadataInterface;
}
