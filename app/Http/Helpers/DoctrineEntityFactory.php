<?php
namespace App\Http\Helpers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;

class DoctrineEntityFactory
{
    /**
     * Create entity manager
     *
     * @return EntityManager
     * @author vduong daiduongptit090@gmail.com
     */
    public function createEntityManager()
    {
        // get configs
        $configDB = $this->getConfig();
        $driver = new AnnotationDriver(new AnnotationReader(), $configDB['paths']);

        $config = Setup::createConfiguration($configDB['isDevMode']);
        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        $entityManager = EntityManager::create($configDB['dbParams'], $config);

        return $entityManager;
    }

    /**
     * Prepare config params
     *
     * @return array
     * @author vduong daiduongptit090@gmail.com
     */
    private function getConfig()
    {
        return [
            'paths' => [__DIR__ . '/../Models/Entities'],
            'isDevMode' => false,
            'dbParams'  => [
                'driver'   => 'pdo_mysql',
                'user'     => (env('DB_USERNAME')) ? env('DB_USERNAME') : 'root',
                'password' => (env('DB_PASSWORD')) ? env('DB_PASSWORD') : '',
                'dbname'   => (env('DB_DATABASE')) ? env('DB_DATABASE') : 'database',
                'host'     => (env('DB_HOST')) ? env('DB_HOST') : 'localhost',
                'port'     => (env('DB_PORT')) ? env('DB_PORT') : 3316
            ]
        ];
    }
}
