<?php

namespace App\Entity;

use App\AbstractSplSubject;
use SplObserver;

abstract class Entity extends AbstractSplSubject
{
    static protected $_defaultEntityManager = null;

    protected $_data = null;

    protected $_em = null;
    protected ?string $_entityName = null;
    protected $_id = null;

    private $newData = null;

    public function init(SplObserver $observer) {
        $this->attach($observer);
    }

    abstract public function getMembers();

    abstract public function getPrimary();

    //setter for properies and items in the underlying data array
    public function __set($variableName, $value)
    {
        if (array_key_exists($variableName, array_change_key_case($this->getMembers()))) {
            $newData = $this->_data;
            $newData[$variableName] = $value;
            $this->update($newData);
        } else {
            if (property_exists($this, $variableName)) {
                $this->$variableName = $value;
            } else {
                throw new \Exception("Set failed. Class " . get_class($this) .
                    " does not have a member named " . $variableName . ".");
            }
        }
    }

    //getter for properies and items in the underlying data array
    public function __get($variableName)
    {
        if (array_key_exists($variableName, array_change_key_case($this->getMembers()))) {
            $data = $this->read();
            return $data[$variableName];
        } else {
            if (property_exists($this, $variableName)) {
                return $this->$variableName;
            } else {
                throw new \Exception("Get failed. Class " . get_class($this) .
                    " does not have a member named " . $variableName . ".");
            }
        }
    }

    static public function setDefaultEntityManager($em)
    {
        self::$_defaultEntityManager = $em;
    }

    //Factory function for making entities.
    static public function getEntity($entityName, $data, $entityManager = null)
    {
        $em = $entityManager === null ? self::$_defaultEntityManager : $entityManager;
        $entity = $em->create($entityName, $data);
        $entity->init($em);
        return $entity;
    }

    static public function getDefaultEntityManager()
    {
        return self::$_defaultEntityManager;
    }

    public function create($entityName, $data)
    {
        return self::getEntity($entityName, $data);
    }

    public function read()
    {
        return $this->_data;
    }

    public function update($newData): void
    {
        $this->notify($newData);
    }

    public function delete()
    {
        $this->_em->delete($this);
    }
}