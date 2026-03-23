<?php

declare(strict_types=1);

namespace Triploide\Toolbox\Http\Controllers;

use Illuminate\Routing\Controller;
use Triploide\Toolbox\Managers as ManagerTraits;

class ApiController extends Controller
{
    use ManagerTraits\Manager;
    use ManagerTraits\PathfinderManager;
    use ManagerTraits\ModelManager;
    use ManagerTraits\AuthorizationManager;
    use ManagerTraits\ValidatorManager;
    use ManagerTraits\DataproviderManager;
    use ManagerTraits\ResponseManager;
    use ManagerTraits\RepositoryManager;
    use Traits\BootstrapTrait;

    private function handleRequest()
    {
        // Validation
        $this->validate();

        // Retrieve information from the database
        $this->retrieve(); // if there is any info to retrieve (must exist the Dataprovider)

        // Authorization
        $this->authorize();

        // Create, edit or delete an entity from the database
        $this->mutate(); // if there is any info to mutate (must exist the Repository)

        // Response
        return $this->reply();
    }

    public function __call($method, $args)
    {
        $this->boot($method);

        return $this->handleRequest();
    }
}