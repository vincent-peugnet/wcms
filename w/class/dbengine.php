<?php

class Dbengine
{
    public function __construct(string $dataDir)
    {
        $this->init($dataDir);
    }

    public function init($dataDir)
    {
              // Handle directory path ending.
        if (substr($dataDir, -1) !== '/') $dataDir = $dataDir . '/';

        if (!file_exists($dataDir)) {
            // The directory was not found, create one.
            if (!mkdir($dataDir, 0777, true)) throw new \Exception('Unable to create the data directory at ' . $dataDir);
        }
      // Check if PHP has write permission in that directory.
        if (!is_writable($dataDir)) throw new \Exception('Data directory is not writable at "' . $dataDir . '." Please change data directory permission.');
      // Finally check if the directory is readable by PHP.
        if (!is_readable($dataDir)) throw new \Exception('Data directory is not readable at "' . $dataDir . '." Please change data directory permission.');
      // Set the data directory.
        $this->dataDirectory = $dataDir;
    }

    // Initialize the store.
    public function store($storeName = false)
    {
        if (!$storeName or empty($storeName)) throw new \Exception('Store name was not valid');
        $this->storeName = $storeName;
        // Boot store.
        $this->bootStore();
        // Initialize variables for the store.
        $this->initVariables();
        return $this;
    }


    public function insert(Art2 $art)
    {
        $artdata = $art->dry();
        $storableJSON = json_encode($artdata);
        if ($storableJSON === false) throw new \Exception('Unable to encode the art object');
        $storePath = $this->storePath . $art->id() . '.json';
        if (!file_put_contents($storePath, $storableJSON)) {
            throw new \Exception("Unable to write the object file! Please check if PHP has write permission.");
        }
        return true;
    }

    public function get($id)
    {
        $filepath = $this->storePath . $id . '.json';
        if (file_exists($filepath)) {
            $data = json_decode(file_get_contents($filepath), true);
            if ($data !== false) return $data;
        } else {
            return false;
        }
    }

    public function update($id, $data)
    {
        foreach ($data as $key => $value) {
                        // Do not update the _id reserved index of a store.
            if ($key != 'id') {
                $data[$key] = $value;
            }
        }
        $storePath = $this->storePath . $id . '.json';
        if (file_exists($storePath)) {
                        // Wait until it's unlocked, then update data.
            file_put_contents($storePath, json_encode($data), LOCK_EX);
        }
        return true;
    }


    public function getidlist()
    {
        $lengh = strlen($this->storePath);
        $list = [];
        foreach (glob($listPath . '*.json') as $filename) {
            $list[] = substr(substr($filename, $lengh), 0, -5);
        }
        return $list;
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
        }
        // Check if PHP has write permission in that directory.
        if (!is_writable($this->storePath)) throw new \Exception('Store path is not writable at "' . $this->storePath . '." Please change store path permission.');
        // Finally check if the directory is readable by PHP.
        if (!is_readable($this->storePath)) throw new \Exception('Store path is not readable at "' . $this->storePath . '." Please change store path permission.');
    }
    
    // Init data that SleekDB required to operate.
    protected function initVariables()
    {
        // Set empty results
        $this->results = [];
        // Set a default limit
        $this->limit = 0;
        // Set a default skip
        $this->skip = 0;
        // Set default conditions
        $this->conditions = [];
        // Set default group by value
        $this->orderBy = [
            'order' => false,
            'field' => '_id'
        ];
        // Set the default search keyword as an empty string.
        $this->searchKeyword = '';
    }




// ______________________________________ analyse _______________________________


   // Find store objects with conditions, sorting order, skip and limits.
    protected function findStoreDocuments()
    {
        $found = [];
        $searchRank = [];
    // Start collecting and filtering data.
        foreach ($this->getidlist() as $id) {
      // Collect data of current iteration.
            $data = $this->get($id);
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



  // Sort store objects.
    protected function sortArray($field, $data, $order = 'ASC')
    {
        $dryData = [];
    // Check if data is an array.
        if (is_array($data)) {
      // Get value of the target field.
            foreach ($data as $value) {
                $dryData[] = $this->getNestedProperty($field, $value);
            }
        }
    // Descide the order direction.
        if (strtolower($order) === 'asc') asort($dryData);
        else if (strtolower($order) === 'desc') arsort($dryData);
    // Re arrange the array.
        $finalArray = [];
        foreach ($dryData as $key => $value) {
            $finalArray[] = $data[$key];
        }
        return $finalArray;
    }

  // Get nested properties of a store object.
    protected function getNestedProperty($field = '', $data)
    {
        if (is_array($data) and !empty($field)) {
      // Dive deep step by step.
            foreach (explode('.', $field) as $i) {
        // If the field do not exists then insert an empty string.
                if (!isset($data[$i])) {
                    $data = '';
                    throw new \Exception('"' . $i . '" index was not found in the provided data array');
                    break;
                } else {
          // The index is valid, collect the data.
                    $data = $data[$i];
                }
            }
            return $data;
        }
    }

  // Do a search in store objects. This is like a doing a full-text search.
    protected function performSerach($data = [])
    {
        if (empty($data)) return $data;
        $nodesRank = [];
    // Looping on each store data.
        foreach ($data as $key => $value) {
      // Looping on each field name of search-able fields.
            foreach ($this->searchKeyword['field'] as $field) {
                try {
                    $nodeValue = $this->getNestedProperty($field, $value);
          // The searchable field was found, do comparison against search keyword.
                    similar_text(strtolower($nodeValue), strtolower($this->searchKeyword['keyword']), $perc);
                    if ($perc > 50) {
            // Check if current store object already has a value, if so then add the new value.
                        if (isset($nodesRank[$key])) $nodesRank[$key] += $perc;
                        else $nodesRank[$key] = $perc;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        if (empty($nodesRank)) {
      // No matched store was found against the search keyword.
            return [];
        }
    // Sort nodes in descending order by the rank.
        arsort($nodesRank);
    // Map original nodes by the rank.
        $nodes = [];
        foreach ($nodesRank as $key => $value) {
            $nodes[] = $data[$key];
        }
        return $nodes;
    }





}









?>