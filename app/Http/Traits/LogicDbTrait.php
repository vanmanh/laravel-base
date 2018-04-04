<?php
namespace App\Http\Traits;

use App\Http\Helpers\DoctrineEntityFactory;
use Doctrine\ORM\ORMException;
use Illuminate\Support\Facades\Log;

trait LogicDbTrait
{
    private $manager = null;
    private $conn = null;

    /**
     * Get doctrine manager
     *
     * @return entityManager
     * @author vduong daiduongptit090@gmail.com
     */
    public function getManager()
    {
        if (!$this->manager) {
            $doctrineFactory = new DoctrineEntityFactory();
            $this->manager = $doctrineFactory->createEntityManager();
        }
        return $this->manager;
    }

    /**
     * Get connection
     *
     * @return Doctrine\DBAL\Connection
     * @author vduong daiduongptit090@gmail.com
     */
    public function getConnection()
    {
        if (!$this->conn) {
            $this->conn = $this->getManager()->getConnection();
        }
        return $this->conn;
    }

    /**
     * Get data from database
     *
     * @param callable $func
     * @author vduong daiduongptit090@gmail.com
     */
    public function readDb($func)
    {
        try{
            $conn = $this->getConnection();
            return $func($this->manager, $conn);
        } catch (ORMException $e) {
            Log::error('An error has occur while get data: ' . $e);
        }
    }

    /**
     * Write data to database
     *
     * @param callable $func
     * @author vduong daiduongptit090@gmail.com
     */
    public function writeDb($func)
    {
        try{
            $conn = $this->getConnection();
            $conn->beginTransaction();
            $result = $func($this->manager, $conn);
            $conn->commit();
            return $result;
        } catch (ORMException $e) {
            Log::error('An error has occur while insert data: ' . $e);
            $conn->rollBack();
            return false;
        }
    }

    /**
     * Insert/update
     *
     * @param string $repository
     * @param array $values
     * @author vduong daiduongptit090@gmail.com
     */
    public function insertOnDuplicate($repository, $values)
    {
        $meta = $this->manager->getClassMetadata($repository);
        $sql = "INSERT INTO " . $meta->getTableName();
        $sql .= " SET " . $this->createPlaceHolders($values);
        $sql .= " ON DUPLICATE KEY UPDATE " . $this->createPlaceHolders($values);
        $stm = $this->getConnection()->prepare($sql);
        $this->createBindParams($stm, $values);
        return $stm->execute();

    }

    private function createPlaceHolders($values)
    {
        $placeHolders = '';
        $columnList = array_keys($values);
        foreach ($columnList as $columnName) {
            $placeHolders[] = $columnName . ' = :' . $columnName;
        }
        return implode(', ' , $placeHolders);
    }

    private function createBindParams($stm, $values)
    {
        foreach ($values as $columnName => $values) {
            $stm->bindValue(':' . $columnName, $values);
        }
    }
}
