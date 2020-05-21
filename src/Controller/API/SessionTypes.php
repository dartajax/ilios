<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionTypeManager;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/{version<v1|v2>}/sessiontypes")
 */
class SessionTypes extends ReadWriteController
{
    public function __construct(SessionTypeManager $manager)
    {
        parent::__construct($manager, 'sessiontypes');
    }
}
