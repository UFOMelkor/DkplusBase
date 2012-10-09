<?php
/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusBase\Service\Crud;

use DkplusBase\Service\Exception\EntityNotFound as EntityNotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginationAdapter;

/**
 * @category   Dkplus
 * @package    Base
 * @subpackage Service\Crud
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class DoctrineMapper implements MapperInterface
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var string */
    protected $modelClass;

    public function __construct(EntityManager $entityManager, $modelClass)
    {
        $this->entityManager = $entityManager;
        $this->modelClass    = $modelClass;
    }

    public function save($item)
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
        return $item;
    }

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function find($identifier)
    {
        $result = $this->entityManager->find($this->modelClass, $identifier);

        if ($result === null) {
            throw new EntityNotFoundException($identifier);
        }

        return $result;
    }

    public function findAll(array $searchData)
    {
        return $this->getQuery($searchData)->execute();
    }

    /**
     * @param array $searchData
     * @return \Doctrine\ORM\Query
     */
    protected function getQuery(array $searchData)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e');
        $queryBuilder->from($this->modelClass, 'e');

        $whereExpressions = array();

        foreach ($searchData as $property => $value) {
            $whereExpressions[] = $queryBuilder->expr()->like('e.' . $property, "%$value%");
        }

        if (count($whereExpressions) > 0) {
            $queryBuilder->where($queryBuilder->expr()->andX($whereExpressions));
        }

        return $queryBuilder->getQuery();
    }

    /**
     * @throws \DkplusBase\Service\Exception\EntityNotFound
     */
    public function delete($identifier)
    {
        $entity = $this->find($identifier);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @param array $searchData
     * @return \Zend\Paginator\Adapter\AdapterInterface
     * @codeCoverageIgnore
     */
    public function getPaginationAdapter(array $searchData)
    {
        $query = $this->getQuery($searchData);
        return new PaginationAdapter(new DoctrinePaginator($query));
    }
}
