<?php

    defined('SYSPATH') or die('No direct script access.');

    class ORM extends Kohana_ORM {

        protected $_primary_keys = array();
        protected $_changed_ext = array();
        protected $_ignore_replace;

        /**
         * Открыть по ID или выкинуть исключение
         *
         * @param type $id
         * @return ORM
         */
        static function get_object_or_404($model_name, $id = null) {
            $model = ORM::factory($model_name);
            if (!$id) {
                $id = Request::current()
                    ->param('id');
            }
            $model
                ->where($model->object_name() . '.' . $model->primary_key(), '=', $id)
                ->find();
            if (!$model->loaded()) {
                throw new HTTP_Exception_404();
            }
            return $model;
        }

        function clear_created_column() {
            $this->_created_column = null;
            return $this;
        }

        function clear_updated_column() {
            $this->_updated_column = null;
            return $this;
        }

        /**
         * @param $field
         * @param $ids
         * @return \ORM
         */
        function where_in($field, $ids) {
            $this->where($field, 'in', DB::expr('(' . implode(',', $ids) . ')'));
            return $this;
        }

        public function __toString() {
            return htmlspecialchars($this->toString(), HTML_ENTITIES);
        }

        /**
         * Finds multiple database rows and returns an iterator of the rows found.
         *
         * @chainable
         * @return  Database_Result
         */
        public function find_all($limit = null, $use_offset = true) {
            if (!empty($this->_load_with)) {
                foreach ($this->_load_with as $alias) {
                    // Bind relationship
                    $this->with($alias);
                }
            }

            $this->_build(Database::SELECT);
            if (null !== $limit) {
                $this->_db_builder->limit($limit);
                $offset = $use_offset ? $limit * (max((int)Arr::get($_GET, 'page', 1), 1) - 1) : 0;
                $this->_db_builder->offset($offset);
            }
            return $this->_load_result(true);
        }

        function labels() {
            $columns = array_keys($this->list_columns());
            return array_combine($columns, $columns);

        }

        public function list_columns() {
            if (Cache::instance()
                ->get('table_columns_' . $this->_object_name)
            ) {
                return Cache::instance()
                    ->get('table_columns_' . $this->_object_name);
            }

            Cache::instance()
                ->set('table_columns_' . $this->_object_name, $this->_db->list_columns($this->table_name()));

            // Proxy to database
            return $this->_db->list_columns($this->table_name());
        }

        public function set($column, $value) {
            if (!isset($this->_object_name)) {
                // Object not yet constructed, so we're loading data from a database call cast
                $this->_cast_data[$column] = $value;

                return $this;
            }

            if (in_array($column, $this->_serialize_columns)) {
                $value = $this->_serialize_value($value);
            }

            if (array_key_exists($column, $this->_object)) {
                // Filter the data
                $value = $this->run_filter($column, $value);

                // See if the data really changed
                if ($value !== $this->_object[$column]) {
                    $this->_changed_ext[$column] = array(
                        'before' => $this->_object[$column],
                        'after'  => $value,
                    );
                    $this->_object[$column]      = $value;

                    // Data has changed
                    $this->_changed[$column] = $column;

                    // Object is no longer saved or valid
                    $this->_saved = $this->_valid = false;
                }
            }
            elseif (isset($this->_belongs_to[$column])) {
                // Update related object itself
                $this->_related[$column] = $value;

                // Update the foreign key of this model
                $this->_object[$this->_belongs_to[$column]['foreign_key']] = ($value instanceof ORM)
                    ? $value->pk()
                    : null;

                $this->_changed[$column] = $this->_belongs_to[$column]['foreign_key'];
            }
            else {
                throw new Kohana_Exception('The :property: property does not exist in the :class: class',
                    array(':property:' => $column,
                          ':class:'    => get_class($this)));
            }

            return $this;
        }

        /**
         * Binds another one-to-one object to this model.  One-to-one objects
         * can be nested using 'object1:object2' syntax
         *
         * @param  string $target_path Target model to bind to
         * @return void
         */
        public function with($target_path) {
            if (isset($this->_with_applied[$target_path])) {
                // Don't join anything already joined
                return $this;
            }

            // Split object parts
            $aliases = explode(':', $target_path);
            $target  = $this;
            foreach ($aliases as $alias) {
                // Go down the line of objects to find the given target
                $parent = $target;
                $target = $parent->_related($alias);

                if (!$target) {
                    // Can't find related object
                    return $this;
                }
            }

            // Target alias is at the end
            $target_alias = $alias;

            // Pop-off top alias to get the parent path (user:photo:tag becomes user:photo - the parent table prefix)
            array_pop($aliases);
            $parent_path = implode(':', $aliases);

            if (empty($parent_path)) {
                // Use this table name itself for the parent path
                $parent_path = $this->_object_name;
            }
            else {
                if (!isset($this->_with_applied[$parent_path])) {
                    // If the parent path hasn't been joined yet, do it first (otherwise LEFT JOINs fail)
                    $this->with($parent_path);
                }
            }

            // Add to with_applied to prevent duplicate joins
            $this->_with_applied[$target_path] = true;

            // Use the keys of the empty object to determine the columns
            foreach (array_keys($target->_object) as $column) {
                $name  = $target_path . '.' . $column;
                $alias = $target_path . ':' . $column;

                // Add the prefix so that load_result can determine the relationship
                $this->select(array($name,
                    $alias));
            }

            if (isset($parent->_belongs_to[$target_alias])) {
                // Parent belongs_to target, use target's primary key and parent's foreign key
                $join_col1  = $target_path . '.' . $target->_primary_key;
                $join_col2  = $parent_path . '.' . $parent->_belongs_to[$target_alias]['foreign_key'];
                $conditions = Arr::path($parent->_belongs_to, $target_alias . '.condition');
            }
            else {
                // Parent has_one target, use parent's primary key as target's foreign key
                $join_col1  = $parent_path . '.' . $parent->_primary_key;
                $join_col2  = $target_path . '.' . $parent->_has_one[$target_alias]['foreign_key'];
                $conditions = Arr::path($parent->_has_one, $target_alias . '.condition');
            }

            // Join the related object into the result
            $this
                ->join(array($target->_table_name,
                    $target_path), 'LEFT')
                ->on($join_col1, '=', $join_col2);
            //Debug::info($parent_path, '$parent_path');
            //Debug::info($target_path, '$target_path');
            //Debug::info($conditions);
            if ($conditions) {
                foreach ($conditions as $condition) {
                    //Debug::info($condition);
                    $col1 = Arr::get($condition, 0);
                    //Debug::info($col1);
                    if (!($col1 instanceof Database_Expression)) {
                        $col1 = str_replace('{parent}', $parent_path, $col1);
                        $col1 = str_replace('{target}', $target_path, $col1);
                    }
                    //Debug::info($col1);
                    $col2 = Arr::get($condition, 2);
                    //Debug::info($col1);
                    //Debug::info($col2);
                    if (!($col2 instanceof Database_Expression)) {
                        $col2 = str_replace('{parent}', $parent_path, $col2);
                        $col2 = str_replace('{target}', $target_alias, $col2);
                    }
                    $this->on($col1, Arr::get($condition, 1), $col2);
                }
            }
            //Debug::stop($this);

            return $this;
        }

        public function toString() {
            return (string)$this->object_name() . ' [' . $this->pk() . ']';
        }

        public function compile($limit = null, $use_offset = true) {
            if (!empty($this->_load_with)) {
                foreach ($this->_load_with as $alias) {
                    // Bind relationship
                    $this->with($alias);
                }
            }

            $this->_build(Database::SELECT);
            if (null !== $limit) {
                $this->_db_builder->limit($limit);
                $offset = $use_offset ? $limit * (max((int)Arr::get($_GET, 'page', 1), 1) - 1) : 0;
                $this->_db_builder->offset($offset);
            }
            $this->_db_builder->from(array($this->_table_name,
                $this->_object_name));

            // Select all columns by default
            $this->_db_builder->select_array($this->_build_select());

            if (!isset($this->_db_applied['order_by']) AND !empty($this->_sorting)) {
                foreach ($this->_sorting as $column => $direction) {
                    if (strpos($column, '.') === false) {
                        // Sorting column for use in JOINs
                        $column = $this->_object_name . '.' . $column;
                    }

                    $this->_db_builder->order_by($column, $direction);
                }
            }
            return $this->_db_builder->compile();
        }

        /**
         * Возврат результатов поиска в виде массива
         * @param type $limit
         * @return type
         */
        function find_all_as_array($limit = null, $key = null) {
            $result = $this
                ->find_all($limit)
                ->as_array($key);
            $ret    = array();
            foreach ($result as $id => $row) {
                $row_data = $row->as_array();
                foreach ($row_data as $k => $v) {
                    $ret[$id][$k] = $v;
                }
            }
            return $ret;
        }

        /**
         * Получение идентификатора записи которая обязана быть ( находится либо создается)
         * @param type $where
         * @param type $default
         * @return type
         */
        function get_or_create_id($where, $default = array()) {
            return $this
                ->get_or_create($where, $default)
                ->pk();
        }

        /**
         * Получить из БД, либо создать и получить в случае отсутствия
         */
        function get_or_create($where, $default = array()) {
            $this->clear();
            $name = $this->object_name();
            foreach ($where as $key => $value) {
                $this->where($name . '.' . $key, '=', $value);
            }
            $this->find();
            //если ничего не найдено!
            if (!$this->loaded()) {
                foreach ($where as $key => $value) {
                    $this->$key = $value;
                }
                $this->create();
                foreach ($default as $key => $value) {
                    $this->$key = $value;
                }
                $this->save();
            }
            return $this;
        }

        public function unique_ext($params) {
            $model = ORM::factory($this->object_name());
            foreach ($params as $field => $value) {
                $model->where($field, '=', $value);
            }
            $model->find();

            if ($this->loaded()) {
                return (!($model->loaded() AND $model->pk() != $this->pk()));
            }

            return (!$model->loaded());
        }

        function replace() {
            if (!count($this->_primary_keys)) {
                throw new \HTTP_Exception('No $this->_primary_keys in model ' . $this->_object_name);
            }
            $values = $this->as_array();
            if (is_array($this->_ignore_replace)) {
                foreach ($this->_ignore_replace as $ignore) {
                    unset($values[$ignore]);
                }
            }
            $db = DB::insert($this->table_name(), array_keys($values))
                ->values($values)
                ->on_dublicate($this->_primary_keys);
            //       print $db->compile().PHP_EOL;
            $db->execute(Database::instance());
        }

        function as_options() {
            return $this->as_array('id');
        }

        function label($field) {
            return Arr::get($this->labels(), $field, $field);
        }

    }