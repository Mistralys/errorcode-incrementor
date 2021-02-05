<?php

    /**
     * The password that has to be specified via GET parameter for
     * the error code to be incremented. This should not be easily
     * guessable, even if the service in itself is not critical.
     */
	define('COUNTERS_PASSWORD', '');

    /**
     * Minimum delay between updates of the same counter, to avoid
     * DOS attacks or spamming counters.
     */
	define('COUNTERS_UPDATE_DELAY', 10);

    /**
     * Add an entry in the array with counter names for all separate
     * counters you wish to use, and the number they should start at.
     */
	$counters = array(
	    'default' => 0
    );
