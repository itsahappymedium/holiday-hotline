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
            'baseHost'          => 'http://' . $_SERVER['HTTP_HOST'],
            'baseUrl'           => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?',
            'voice'             => 'woman',
            'language'          => 'en-gb'
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
        $this->_gather(array(
            'action'        => $this->options['baseUrl'] . http_build_query( array(
                'action' => 'baking_menu'
            )),
            'numDigits'     => 1,
            'finishOnKey'   => 'any digit'
        ), "
            Press 1 for Turkey.
            Press 2 for Ham.
            Press 3 for Duck.
            Press 9 to return to the main menu.
            Press 0 to repeat the options.
        ");

        return $this->response;
    }

    /**
     * Print out baking tips
     * @param  string $food Food type
     * @return object       Response
     */
    public function baking_tips( $food ) {

        switch ( $food ) {
            case 'turkey':
                $this->_say("
                    Tips for cooking turkey:
                    Thawing a frozen turkey requires patience.
                    The safest method is to thaw turkey in the refrigerator.
                    Cooking times will differ depending on whether your bird was purchased fresh or frozen.
                    Remember to carve your turkey with a very sharp or electric knife.
                ");

                break;

            case 'ham':
                $this->_say("
                    Tips for cooking ham:
                    Almost all hams have either been partially or fully cooked before they are packaged.
                    A partially cooked ham has been brought to an internal temperature
                    of 137 degrees fahrenheit, which kills any bacteria.
                    A fully cooked ham will require about 10 minutes per pound in
                    order to be heated all the way through.
                ");

                break;

            case 'duck':
                $this->_say("
                    Tips for cooking duck:
                    By properly cooking duck, you can eliminate up to 70% of the fat,
                    which leaves a delicious, crisp skin.
                    Confit duck comes from an old method of preserving meat by seasoning
                    it and slowly cooking it in its own fat. The cooked meat was then packed
                    into a crock and covered with its cooking fat which acted as a seal and
                    preservative. This method produces a particularly tender meat.
                ");

                break;

            default:
                $this->baking_menu();

                break;
        }

        // Return to the baking menu afterward
        $this->baking_menu();

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
            Press 3 for winter travel tips.
            Press 0 to repeat the options.
        ");

        return $this->response;
    }

    /**
     * Play a holiday carol
     * @return object Response
     */
    public function play_carol() {
        $this->response->play( $this->options['baseHost'] . '/assets/let_it_snow.mp3' );

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

                        case 3:
                            $this->winter_travel_tips();

                        case 0:
                            $this->main_menu();

                        default:
                            $this->main_menu();

                            break;
                    }

                    break;

                /**
                 * Someone chose an option from the baking menu
                 */
                case 'baking_menu':
                    if ( ! is_int($digits) ) {
                        $this->main_menu();

                        return;
                    }

                    switch ( $digits ) {
                        case 1:
                            $this->baking_tips('turkey');

                            break;

                        case 2:
                            $this->baking_tips('ham');

                            break;

                        case 3:
                            $this->baking_tips('duck');

                            break;

                        case 9:
                            $this->main_menu();

                            break;

                        case 0:
                            $this->baking_menu();

                            break;

                        default:
                            $this->baking_menu();

                            break;
                    }

                default:
                    $this->main_menu();

                    break;
            }
        } else {
            // If no action or digits, do welcome
            $this->welcome();
        }

        // Print once
        print $this->response;

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

    /**
     * Print out winter travel tips
     * @return object Response
     */
    public function winter_travel_tips() {
        $this->_say("
            Winter driving trips:
            Avoid driving while you’re fatigued. Getting the proper amount of rest
            before taking on winter weather tasks reduces driving risks.
            Never warm up a vehicle in an enclosed area, such as a garage.
            Make certain your tires are properly inflated.
            Never mix radial tires with other tire types.
            Keep your gas tank at least half full to avoid gas line freeze-up.
            If possible, avoid using your parking brake in cold, rainy and snowy weather.
        ");

        $this->main_menu();

        return $this->response;
    }
}

// Execute
new Holiday_Hotline();