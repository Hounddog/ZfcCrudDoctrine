<?php

namespace ZfcCrudDoctrine\Mapper;

use Doctrine\ORM\EntityManager;
use Zend\Stdlib\Hydrator\HydratorInterface;

use ZfcCrud\Mapper\DbMapperInterface;

class DoctrineDb implements DbMapperInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $entityClassName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findAll($where = null, HydratorInterface $hydrator = null) 
    {
        $er = $this->em->getRepository($this->entityClassName);
        $results = $er->findAll();
        if(null !== $hydrator) {
            $data = array();
            foreach($results as $entity) {
                $data[] = $hydrator->extract($entity);
            }
            $results = $data;
        }
        return $results;
    }

    public function findById($id)
    {
        $er = $this->em->getRepository($this->entityClassName);
        return $er->find($id);
    }

    public function create($entity)
    {
        return $this->persist($entity);
    }

    public function update($entity)
    {
        return $this->persist($entity);
    }

    public function delete($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    protected function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    public function setEntityClassName($entityClassName)
    {
        $this->entityClassName = $entityClassName;
    }

    public function getEntityClassName() 
    {
        return $this->entityClassName;
    }

    /**
     * Uses the hydrator to convert the entity to an array.
     *
     * Use this method to ensure that you're working with an array.
     *
     * @param object $entity
     * @return array
     */
    protected function entityToArray($entity, HydratorInterface $hydrator = null)
    {
        if (is_array($entity)) {
            return $entity; // cut down on duplicate code
        } elseif (is_object($entity)) {
            if (!$hydrator) {
                $hydrator = $this->getHydrator();
            }
            return $hydrator->extract($entity);
        }
        throw new Exception\InvalidArgumentException('Entity passed to db mapper should be an array or object.');
    }
}