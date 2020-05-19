<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshTermManager;

class MeshTerm extends ReadOnlyController
{
    public function __construct(MeshTermManager $manager)
    {
        parent::__construct($manager, 'meshterms');
    }
}
