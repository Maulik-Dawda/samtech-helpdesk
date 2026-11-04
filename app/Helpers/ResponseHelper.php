<?php

class ResponseHelper
{
    /**
     * Closes the session and finishes the HTTP response to the client immediately.
     * This releases the browser/tab instantly so the user sees the redirected page
     * in milliseconds, while PHP continues running email notifications in the background.
     */
    public static function finishRequestAndContinue(): void
    {
        // 1. CRITICAL: Save and release the PHP session lock immediately!
        // Without this, the redirected HTTP request will block on session_start()
        // until the entire script finishes sending emails.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // 2. Prevent script termination when user disconnects / navigates away
        ignore_user_abort(true);

        // 3. Extend execution time limit so background mail sending doesn't time out
        @set_time_limit(180);

        // 4. Try FastCGI finish request (PHP-FPM)
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
            return;
        }

        // 5. Try LiteSpeed finish request (LiteSpeed Web Server / cPanel LSAPI)
        if (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
            return;
        }

        // 6. Generic web server fallback (Apache mod_php / generic CGI)
        if (!headers_sent()) {
            header("Connection: close");
        }

        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        @flush();
    }
}
