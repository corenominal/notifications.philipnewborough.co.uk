<?php

namespace App\Controllers\CLI;

use App\Controllers\BaseController;
use CodeIgniter\CLI\CLI;

/**
 * @internal
 */
final class Test extends BaseController
{
    /**
     * Index method for the CLI Test controller.
     *
     * This method returns a greeting message.
     * 
     * Usage example:
     * sudo -u _www php /path/to/codeigniter/public/index.php cli/test index "Jaja Binks"
     *
     * @param string $to The name or entity to greet. Defaults to 'World'.
     * @return string The greeting message.
     */
    public function index($to = 'World')
    {
        return "Hello, {$to}!";
    }

    /**
     * Counts from 1 to 10 with a 1-second interval between each count.
     * 
     * This method uses the CLI class to display the current count in the terminal.
     * It overwrites the same line with the updated count using a carriage return.
     * After completing the count, it outputs a new line and writes "Done." to the terminal.
     * 
     * Usage example:
     * sudo -u _www php /path/to/codeigniter/public/index.php cli/test count
     * 
     * @return void
     */
    public function count()
    {
        for ($i = 1; $i <= 10; $i++) {
            CLI::print("\rCounting: $i");
            sleep(1); // Wait for 1 second
        }
        CLI::newLine();
        CLI::write('Done.');
    }
}
