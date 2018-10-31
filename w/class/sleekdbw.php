<?php

use SleekDB\SleekDB;


class SleekDBw extends SleekDB
{

    public function getStoreIdList()
    {
        $listPath = $this->storePath . 'data/';
        $lengh = strlen($listPath);
        $list = [];
        foreach (glob($listPath . '*.json') as $filename) {
            $list[] = substr(substr($filename, $lengh), 0, -5);
        }
        return $list;
    }

    public function getbyid($id)
    {
        return $this->getStoreDocumentById($id);
    }

    public function updatebyid($id, $data)
    {
        foreach ($data as $key => $value) {
                        // Do not update the _id reserved index of a store.
            if ($key != '_id') {
                $data[$key] = $value;
            }
        }
        $storePath = $this->storePath . 'data/' . $id . '.json';
        if (file_exists($storePath)) {
                        // Wait until it's unlocked, then update data.
            file_put_contents($storePath, json_encode($data), LOCK_EX);
        }
                                // Check do we need to wipe the cache for this store.
        if ($this->deleteCacheOnCreate === true) $this->_emptyAllCache();
        return true;
    }


      



            // Writes an object in a store.
    protected function writeInStore($storeData)
    {
                      // Cast to array
        $storeData = (array)$storeData;
                      // Check if it has _id key
        if (empty($storeData['_id'])) throw new \Exception('Lack of id in this object');
        if (in_array($storeData['_id'], $this->getStoreIdList())) throw new \Exception('Id already used');
                        // Add the system ID with the store data array.
        $id = $storeData['_id'];
                      // Prepare storable data
        $storableJSON = json_encode($storeData);
        if ($storableJSON === false) throw new \Exception('Unable to encode the data array, please provide a valid PHP associative array');
                      // Define the store path
        $storePath = $this->storePath . 'data/' . $id . '.json';
        if (!file_put_contents($storePath, $storableJSON)) {
            throw new \Exception("Unable to write the object file! Please check if PHP has write permission.");
        }
        return $storeData;
    }
                  
            
            // Find store objects with conditions, sorting order, skip and limits.
    protected function findStoreDocuments()
    {
        $found = [];
        $storeIdList = $this->getStoreIdList();
        $searchRank = [];
                // Start collecting and filtering data.
        foreach ($storeIdList as $id) {
                  // Collect data of current iteration.
            $data = $this->getStoreDocumentById($id);
            if (!empty($data)) {
                    // Filter data found.
                if (empty($this->conditions)) {
                      // Append all data of this store.
                    $found[] = $data;
                } else {
                      // Append only passed data from this store.
                    $storePassed = true;
                      // Iterate each conditions.
                    foreach ($this->conditions as $condition) {
                        // Check for valid data from data source.
                        $validData = true;
                        $fieldValue = '';
                        try {
                            $fieldValue = $this->getNestedProperty($condition['fieldName'], $data);
                        } catch (\Exception $e) {
                            $validData = false;
                            $storePassed = false;
                        }
                        if ($validData === true) {
                          // Check the type of rule.
                            if ($condition['condition'] === '=') {
                            // Check equal.
                                if ($fieldValue != $condition['value']) $storePassed = false;
                            } else if ($condition['condition'] === '!=') {
                            // Check not equal.
                                if ($fieldValue == $condition['value']) $storePassed = false;
                            } else if ($condition['condition'] === '>') {
                            // Check greater than.
                                if ($fieldValue <= $condition['value']) $storePassed = false;
                            } else if ($condition['condition'] === '>=') {
                            // Check greater equal.
                                if ($fieldValue < $condition['value']) $storePassed = false;
                            } else if ($condition['condition'] === '<') {
                            // Check less than.
                                if ($fieldValue >= $condition['value']) $storePassed = false;
                            } else if ($condition['condition'] === '<=') {
                            // Check less equal.
                                if ($fieldValue > $condition['value']) $storePassed = false;
                            }
                        }
                    }
                      // Check if current store is updatable or not.
                    if ($storePassed === true) {
                        // Append data to the found array.
                        $found[] = $data;
                    }
                }
            }
        }
        if (count($found) > 0) {
                  // Check do we need to sort the data.
            if ($this->orderBy['order'] !== false) {
                    // Start sorting on all data.
                $found = $this->sortArray($this->orderBy['field'], $found, $this->orderBy['order']);
            }
                  // If there was text search then we would also sort the result by search ranking.
            if (!empty($this->searchKeyword)) {
                $found = $this->performSerach($found);
            }
                  // Skip data
            if ($this->skip > 0) $found = array_slice($found, $this->skip);
                  // Limit data.
            if ($this->limit > 0) $found = array_slice($found, 0, $this->limit);
        }
        return $found;
    }



        // Method to boot a store.
    protected function bootStore()
    {
        $store = trim($this->storeName);
            // Validate the store name.
        if (!$store || empty($store)) throw new \Exception('Invalid store name was found');
            // Prepare store name.
        if (substr($store, -1) !== '/') $store = $store . '/';
            // Store directory path.
        $this->storePath = $this->dataDirectory . $store;
            // Check if the store exists.
        if (!file_exists($this->storePath)) {
              // The directory was not found, create one with cache directory.
            if (!mkdir($this->storePath, 0777, true)) throw new \Exception('Unable to create the store path at ' . $this->storePath);
              // Create the cache directory.
            if (!mkdir($this->storePath . 'cache', 0777, true)) throw new \Exception('Unable to create the store\'s cache directory at ' . $this->storePath . 'cache');
              // Create the data directory.
            if (!mkdir($this->storePath . 'data', 0777, true)) throw new \Exception('Unable to create the store\'s data directory at ' . $this->storePath . 'data');
            // Check if PHP has write permission in that directory.
            if (!is_writable($this->storePath)) throw new \Exception('Store path is not writable at "' . $this->storePath . '." Please change store path permission.');
            // Finally check if the directory is readable by PHP.
            if (!is_readable($this->storePath)) throw new \Exception('Store path is not readable at "' . $this->storePath . '." Please change store path permission.');
        }
    }




}

?>