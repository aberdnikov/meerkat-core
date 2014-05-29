    <?php

        defined('SYSPATH') OR die('No direct access allowed.');

        /**
         * Default auth user
         *
         * @package    Kohana/Auth
         * @author     Kohana Team
         * @copyright  (c) 2007-2012 Kohana Team
         * @license    http://kohanaframework.org/license
         */
        class Kohana_Model_User extends ORM {

            static $gender_text = array(
                1  => 'мужчина',
                -1 => 'женщина',
                0  => 'не определился',
            );
            static $gender_ico = array(
                0  => 'iconfam_user_gray',
                1  => 'iconfam_user',
                -1 => 'iconfam_user_female',
            );

            function __toString() {
                return $this->loaded() ? $this->username : '';
            }

            /**
             * Filters to run when data is set in this model. The password filter
             * automatically hashes the password when it's set in the model.
             *
             * @return array Filters
             */
            public function filters() {
                return array(
                    'password' => array(
                        array(array(Auth::instance(),
                            'hash'))
                    )
                );
            }

            function labels() {
                return array(
                    'email'         => 'E-mail',
                    'username'      => 'Имя',
                    'login'         => 'Логин',
                    'password'      => 'Пароль',
                    'regdate'       => 'Дата регистрации',
                    'last_login'    => 'Последний раз был на сайте',
                    'is_man'        => 'Пол',
                    'is_admin'      => 'Администратор?',
                    'is_ban'        => 'Забанен?',
                    'site'          => 'Сайт',
                    'phone'         => 'Телефон',
                    'about'         => 'Обо мне',
                    'activate_code' => 'Код активации аккаунта',
                    'lostpass_code' => 'Код восстановления пароля',
                    'email_code'    => 'Код для смены e-mail',
                );
            }

            /**
             * Complete the login for a user by incrementing the logins and saving login timestamp
             *
             * @return void
             */
            public function complete_login() {
                if ($this->_loaded) {
                    // Update the number of logins
                    $this->logins = new Database_Expression('logins + 1');

                    // Set the last login date
                    $this->last_login = time();

                    // Save the user
                    $this->update();
                }
            }

            /**
             * Tests if a unique key value exists in the database.
             *
             * @param   mixed    the value to test
             * @param   string   field name
             * @return  boolean
             */
            public function unique_key_exists($value, $field = null) {
                if ($field === null) {
                    // Automatically determine field by looking at the value
                    $field = $this->unique_key($value);
                }

                return (bool)DB::select(array(DB::expr('COUNT(*)'),
                    'total_count'))
                    ->from($this->_table_name)
                    ->where($field, '=', $value)
                    ->where($this->_primary_key, '!=', $this->pk())
                    ->execute($this->_db)
                    ->get('total_count');
            }

            /**
             * Allows a model use both email and username as unique identifiers for login
             *
             * @param   string  unique value
             * @return  string  field name
             */
            public function unique_key($value) {
                return Valid::email($value) ? 'email' : 'login';
            }

        }

        // End Auth User Model
