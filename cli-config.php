<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use App\Http\Helpers\DoctrineEntityFactory;

// replace with file to your own project bootstrap
require_once 'bootstrap/autoload.php';

// replace with mechanism to retrieve EntityManager in your app
$doctrineFactory = new DoctrineEntityFactory();
$entityManager = $doctrineFactory->createEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
