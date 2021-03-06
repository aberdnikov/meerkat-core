<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * ORM Auth driver.
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Auth_Meerkat extends Auth {

    /**
     * Checks if a session is active.
     *
     * @param   mixed    $role Role name string, role ORM object, or array with role names
     * @return  boolean
     */
    public function logged_in($role = NULL) {
        // Get the user from the session
        $user = $this->get_user();

        return (!$user) ? false : true;
    }

    /**
     * Logs a user in.
     *
     * @param   string   username
     * @param   string   password
     * @param   boolean  enable autologin
     * @return  boolean
     */
    protected function _login($username, $password, $remember) {
        // Load the user
        $user = ORM::factory('User');
        $field = Valid::email($username) ? 'email' : 'login';
        $user->where($field, '=', $username)->find();
        // Create a hashed password
        $password = Auth::instance()->hash($password);
        //Debug::info($user->password);
        //Debug::stop($password);
        // If the passwords match, perform a login
        if ($user->password === $password) {
            if ($user->is_ban && !$user->is_admin) {
                Meerkat\Widget\Widget_Alert::factory('Ваш аккаунт заблокирован!')
                        ->as_error()
                        ->put();
                HTTP::redirect('/', 302);
            }
            if ($remember === TRUE) {
                // Token data
                $data = array(
                    'user_id' => $user->id,
                    'expires' => time() + $this->_config['lifetime'],
                    'user_agent' => sha1(Request::$user_agent),
                );

                // Create a new autologin token
                $token = ORM::factory('user_token')
                        ->values($data)
                        ->create();
                // Set the autologin cookie
                Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
            }
            // Finish the login
            $this->complete_login($user);
            //событие юзер залогинился
            $event = new sfEvent(NULL, 'Auth_Meerkat::complete_login');
            \Meerkat\Event\Event::dispatcher()->notify($event);
            return TRUE;
        }

        // Login failed
        return FALSE;
    }

    /**
     * Forces a user to be logged in, without specifying a password.
     *
     * @param   mixed    username string, or user ORM object
     * @param   boolean  mark the session as forced
     * @return  boolean
     */
    public function force_login($user, $mark_session_as_forced = FALSE) {
        if (!is_object($user)) {
            $username = $user;
            // Load the user
            $user = ORM::factory('User');
            $user->where($user->unique_key($username), '=', $username)->find();
        }

        if ($mark_session_as_forced === TRUE) {
            // Mark the session as forced, to prevent users from changing account information
            $this->_session->set('auth_forced', TRUE);
        }
        // Run the standard completion
        $this->complete_login($user);
        //событие юзер залогинился
        $event = new sfEvent(NULL, 'Auth_Meerkat::complete_login');
        \Meerkat\Event\Event::dispatcher()->notify($event);
    }

    /**
     * Logs a user in, based on the authautologin cookie.
     *
     * @return  mixed
     */
    public function auto_login() {
        if ($token = Cookie::get('authautologin')) {
            // Load the token and user
            $token = ORM::factory('user_token', array('token' => $token));

            if ($token->loaded() AND $token->user->loaded()) {
                if ($token->user_agent === sha1(Request::$user_agent)) {
                    // Save the token to create a new unique token
                    $token->save();

                    // Set the new token
                    Cookie::set('authautologin', $token->token, $token->expires - time());

                    // Complete the login with the found data
                    $this->complete_login($token->user);

                    // Automatic login was successful
                    return $token->user;
                }

                // Token is invalid
                $token->delete();
            }
        }

        return FALSE;
    }

    /**
     * Gets the currently logged in user from the session (with auto_login check).
     * Returns FALSE if no user is currently logged in.
     *
     * @return  mixed
     */
    public function get_user($default = NULL) {
        $user = parent::get_user($default);

        if (!$user) {
            // check for "remembered" login
            $user = $this->auto_login();
        }

        return $user;
    }

    /**
     * Log a user out and remove any autologin cookies.
     *
     * @param   boolean  completely destroy the session
     * @param	boolean  remove all tokens for user
     * @return  boolean
     */
    public function logout($destroy = FALSE, $logout_all = FALSE) {
        // Set by force_login()
        $this->_session->delete('auth_forced');

        if ($token = Cookie::get('authautologin')) {
            // Delete the autologin cookie to prevent re-login
            Cookie::delete('authautologin');

            // Clear the autologin token from the database
            $token = ORM::factory('user_token', array('token' => $token));

            if ($token->loaded() AND $logout_all) {
                ORM::factory('user_token')->where('user_id', '=', $token->user_id)->delete_all();
            } elseif ($token->loaded()) {
                $token->delete();
            }
        }

        return parent::logout($destroy);
    }

    /**
     * Get the stored password for a username.
     *
     * @param   mixed   username string, or user ORM object
     * @return  string
     */
    public function password($user) {
        if (!is_object($user)) {
            $username = $user;

            // Load the user
            $user = ORM::factory('User');
            $user->where($user->unique_key($username), '=', $username)->find();
        }

        return $user->password;
    }

    /**
     * Complete the login for a user by incrementing the logins and setting
     * session data: user_id, username, roles.
     *
     * @param   object  user ORM object
     * @return  void
     */
    protected function complete_login($user) {
        $user->last_login = date('Y-m-d H:i:s');
        $user->update();
        //если есть openid надо его прикрепить к созданному аккаунту
        /* $account = Session::instance()->get(Kohana::$config->load('loginza.session_key'));
          if ($account) {
          $id = Arr::get($account, 'id');
          $openid_account = ORM::factory('openid_accounts', $id);
          if ($openid_account->loaded()) {
          $openid_account->user_id = $user->pk();
          $openid_account->save();
          $thumb = Cinderella_Thumb::factory('user', $user->pk());
          $avatars = $thumb->get_all();
          if (!$avatars && $openid_account->photo) {
          $thumb->make($openid_account->photo);
          }
          if (!$user->email && $openid_account->email) {
          $user->email = $openid_account->email;
          $user->save();
          }
          }
          Session::instance()->delete(Kohana::$config->load('loginza.session_key'));
          } */
        return parent::complete_login($user->as_array());
    }

    /**
     * Compare password with original (hashed). Works for current (logged in) user
     *
     * @param   string  $password
     * @return  boolean
     */
    public function check_password($password) {
        $user = $this->get_user();

        if (!$user)
            return FALSE;

        return ($this->hash($password) === Helper_User::password());
    }

}

// End Auth ORM