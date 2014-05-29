<?php

class HTTP_Exception extends Kohana_HTTP_Exception {

    /**
     * Generate a Response for all Exceptions without a more specific override
     *
     * The user should see a nice error page, however, if we are in development
     * mode we should show the normal Kohana error page.
     *
     * @return Response
     */
    public function get_response() {
        // Lets log the Exception, Just in case it's important!
        Kohana_Exception::log($this);

        if (Kohana::$environment >= Kohana::DEVELOPMENT) {
        //if (0) {
            // Show the normal Kohana error page.
            return parent::get_response();
        } else {
            return self::_handler($this);
            //var_dump(get_included_files());
            //var_dump(Route::all());
            //var_dump($this->getTrace());
            //exit($this->getTraceAsString());
            $params = array                (
                'action' => $this->getCode(),
                'message' => rawurlencode($this->getMessage())
            );
            // Error sub-request.
            return Request::factory(Route::get('error')->uri($params))
                    ->execute();
        }
    }

}