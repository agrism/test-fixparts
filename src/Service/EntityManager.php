<?php

namespace App\Service;

use App\AbstractSplSubject;
use App\Entity\Entity;
use ReturnTypeWillChange;
use SplObjectStorage;
use SplObserver;

class EntityManager extends AbstractSplSubject implements SplObserver
{

    protected $_entities = array();

    protected $_entityIdToPrimary = array();

    protected $_entityPrimaryToId = array();

    protected $_entitySaveList = array();

    protected $_nextId = null;

    protected $_dataStore = null;

    /**
     * @throws \Exception
     */
    public function __construct(DataStore $dataStore)
    {
        parent::__construct();

        $this->_dataStore = $dataStore;

        $this->_nextId = 1;

        $itemTypes = $this->_dataStore->getItemTypes();
        foreach ($itemTypes as $itemType)
        {
            $itemKeys = $this->_dataStore->getItemKeys($itemType);
            foreach ($itemKeys as $itemKey) {
                $this->create($itemType, $this->_dataStore->get($itemType, $itemKey), true);
            }
        }
    }

    //create an entity
    public function create($entityName, $data, $fromStore = false)
    {
        $entity = new $entityName;
        $entity->_entityName = $entityName;
        $entity->_data = $data;
        $entity->_em = Entity::getDefaultEntityManager();
        $id = $entity->_id = $this->_nextId++;
        $this->_entities[$id] = $entity;
        $primary = $data[$entity->getPrimary()];
        $this->_entityIdToPrimary[$id] = $primary;
        $this->_entityPrimaryToId[$primary] = $id;
        if ($fromStore !== true) {
            $this->_entitySaveList[] = $id;
        }

        return $entity;
    }

    /**
     * @param Entity $entity
     * @param $newData
     * @return Entity
     */
    #[ReturnTypeWillChange] public function update($entity, $newData = null)
    {
        if ($newData === $entity->_data) {
            //Nothing to do
            return $entity;
        }

        $this->_entitySaveList[] = $entity->_id;

        $primary = $entity->getPrimary();

        $oldPrimary = $entity->{$primary};
        $newPrimary = $newData[$primary];

        if ($oldPrimary != $newPrimary)
        {
            $this->_dataStore->delete(get_class($entity),$oldPrimary);
            unset($this->_entityPrimaryToId[$oldPrimary]);

            $this->_entityIdToPrimary[$entity->_id] = $newPrimary;
            $this->_entityPrimaryToId[$newPrimary] = $entity->_id;
        }

        $entity->_data = $newData;

        $this->notify($newData);

        return $entity;
    }

    //Delete
    public function delete($entity)
    {
        $id = $entity->_id;
        $entity->_id = null;
        $entity->_data = null;
        $entity->_em = null;
        $this->_entities[$id] = null;
        $primary = $entity->{$entity->getPrimary()};
        $this->_dataStore->delete(get_class($entity),$primary);
        unset($this->_entityIdToPrimary[$id]);
        unset($this->_entityPrimaryToId[$primary]);
        return null;
    }

    public function findByPrimary($entity, $primary)
    {
        if (isset($this->_entityPrimaryToId[$primary])) {
            $id = $this->_entityPrimaryToId[$primary];
            return $this->_entities[$id];
        } else {
            return null;
        }
    }

    //Update the datastore to update itself and save.
    public function updateStore() {
        foreach($this->_entitySaveList as $id) {
            $entity = $this->_entities[$id];
            $this->_dataStore->set(get_class($entity),$entity->{$entity->getPrimary()},$entity->_data);
        }
        $this->_dataStore->save();
    }

    public function findAll()
    {
        return $this->_dataStore->getAll();
    }
}