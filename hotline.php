<?php

/**
 * Holiday Hotline Class
 */

class Holiday_Hotline {

    /**
     * Options
     * @var array
     */
    var $options;

    /**
     * Response
     * @var object
     */
    var $response;

    function __construct() {
        // Include Vendor classes
        try {
            require('./vendor/autoload.php');
        } catch (Exception $e) {
            echo 'You must install vendor dependencies with "composer install"';
        }

        // Set options for voice
        $this->options = array(
            'baseUrl'           => 'http://hotline.itsahappymedium.com/hotline.php?',
            'voice'             => 'woman',
            'language'          => 'en-gb',
            'baking_options'    => array(
                'turkey',
                'ham',
                'duck'
            )
        );

        // Set our response headers
        header('Content-Type: application/xml; charset=utf-8');

        // Initialize our response TWIML object
        $this->response = new Services_Twilio_Twiml;

        // Process the input
        $digits = isset( $_REQUEST['Digits'] ) ? (int)$_REQUEST['Digits'] : null;
        $action = isset( $_GET['action'] ) ? $_GET['action'] : null;
        $this->process_input( $action, $digits );
    }

    /**
     * Call a gather statement with the passed options
     * @param  array $options  Options for the Gather statement
     * @return object          Response object
     */
    public function _gather( $options, $message ) {
        $this->response->gather( $options )
             ->say( $message, array(
                'voice'     => $this->options['voice'],
                'language'  => $this->options['language']
            ));

        return $this->response;
    }

    /**
     * Say message with defaults
     * @param  string $message  Message to say
     * @return object           Modified TWIML object
     */
    public function _say( $message ) {
        $this->response->say(
            $message,
            array(
                'voice'     => $this->options['voice'],
                'language'  => $this->options['language']
            )
        );

        return $this->response;
    }

    /**
     * Print out a baking menu
     * @return object Response
     */
    public function baking_menu() {
        $this->gather(array(
            'action'        => $this->options['baseUrl'] . http_build_query( array(
                'action' => 'baking_menu'
            )),
            'numDigits'     => 1,
            'finishOnKey'   => 'any digit'
        ), "
            Pick an option.
        ");

        print $this->response;

        return $this->response;
    }

    /**
     * Speak and gather the main menu
     * @return object Response
     */
    public function main_menu() {
        $this->_gather(array(
            'action'        => $this->options['baseUrl'] . http_build_query( array(
                'action' => 'main_menu'
            )),
            'numDigits'     => 1,
            'finishOnKey'   => 'any digit'
        ), "
            Press 1 for a holiday carol sung by Happy Medium team members.
            Press 2 for a holiday baking tip.
            Press 3 for a dance party.
        ");

        print $this->response;

        return $this->response;
    }

    /**
     * Play a holiday carol
     * @return object Response
     */
    public function play_carol() {
        $this->_say("
            This is a holiday carol.
        ");

        $this->main_menu();

        return $this->response;
    }

    /**
     * Process the input passed to the hotline
     * @param  string $action Action (optional)
     * @param  int $digits    Digits (optional)
     * @return this           Self
     */
    public function process_input( $action, $digits ) {
        if ( ! empty($action) ) {
            // Do something
            switch ( $action ) {
                /**
                 * Someone chose an option from the main menu
                 */
                case 'main_menu':

                    if ( ! is_int($digits) ) {
                        $this->main_menu();

                        return;
                    }

                    switch ( $digits ) {
                        case 1:
                            $this->play_carol();

                            break;

                        case 2:
                            $this->baking_menu();

                            break;

                        default:
                            $this->main_menu();

                            break;
                    }

                    break;

                default:
                    $this->main_menu();

                    break;
            }
        } else {
            // If no action or digits, do main menu
            $this->welcome();
        }

        return $this;
    }

    /**
     * Say a welcome message
     * @return object Response
     */
    public function welcome() {
        $this->_say("Welcome to the Happy Medium Holiday Hotline, spreading Christmas cheer since 2014.");

        // Automatically start the main menu, which prints the response
        $this->main_menu();

        return $this->response;
    }
}

// Execute
new Holiday_Hotline();