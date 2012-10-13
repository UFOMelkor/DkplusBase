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

    /** @var string */
    protected $orderCrit;

    /** @var string */
    protected $orderDirection = 'ASC';

    /** @var boolean */
    protected $allCritsMustMatch = true;

    public function __construct(EntityManager $entityManager, $modelClass)
    {
        $this->entityManager  = $entityManager;
        $this->modelClass     = $modelClass;
    }

    public function setOnlyOneSearchCriteriumMustMatch($flag)
    {
        $this->allCritsMustMatch = !$flag;
    }

    /**
     * @param string $orderCrit
     * @param string $orderDirection
     */
    public function setDefaultOrderBy($orderCrit, $orderDirection = 'ASC')
    {
        $this->orderCrit      = $orderCrit;
        $this->orderDirection = $orderDirection;
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

    public function findAll(array $searchData, $orderCrit = null, $orderDirection = null)
    {
        return $this->getQuery($searchData, $orderCrit, $orderDirection)->execute();
    }

    /**
     * @param array $searchData
     * @param string $orderCrit
     * @param string $orderDirection
     * @return \Doctrine\ORM\Query
     */
    protected function getQuery(array $searchData, $orderCrit, $orderDirection)
    {
        $orderCrit = $orderCrit === null
                   ? $this->orderCrit
                   : $orderCrit;
        $orderDirection = $orderDirection === null
                        ? $this->orderDirection
                        : $orderDirection;

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e');
        $queryBuilder->from($this->modelClass, 'e');

        if ($orderCrit !== null) {
            $queryBuilder->orderBy('e.' . $orderCrit, $orderDirection);
        }

        if (count($searchData) > 0) {
            $conjunction = $this->allCritsMustMatch
                         ? $queryBuilder->expr()->andX()
                         : $queryBuilder->expr()->orX();
            foreach ($searchData as $property => $value) {
                $expression = is_numeric($value)
                            ? $queryBuilder->expr()->eq('e.' . $property, $value)
                            : $queryBuilder->expr()->like('e.' . $property, "%$value%");
                $conjunction->add($expression);
            }
            $queryBuilder->where($conjunction);
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
    public function getPaginationAdapter(array $searchData, $orderCrit = null, $orderDirection = null)
    {
        $query = $this->getQuery($searchData, $orderCrit, $orderDirection);
        return new PaginationAdapter(new DoctrinePaginator($query));
    }
}
