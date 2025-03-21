<?php

/**
 * Recursively delete files older than a certain period from a directory.
 *
 * @param string $directory The path to the directory.
 * @param int $timePeriod The time period in seconds.
 */
function deleteExpiredFiles($directory, $timePeriod) {
    // Get the current time
    $currentTime = time();

    // Open the directory
    if ($handle = opendir($directory)) {
        // Loop through the files and directories
        while (false !== ($file = readdir($handle))) {
            // Skip . and .. special directories
            if ($file != '.' && $file != '..') {
                $filePath = $directory . '/' . $file;

                // If it's a directory, recursively call the function
                if (is_dir($filePath)) {
                    deleteExpiredFiles($filePath, $timePeriod);
					 // Remove the directory if it's empty
                    if (count(scandir($filePath)) == 2) { // . and ..
                        rmdir($filePath);
                        echo "Deleted directory: $filePath\n";
                    }
                } else {
                    // If it's a file, check if it's older than the time period
                    $fileModTime = filemtime($filePath);

                    if ($currentTime - $fileModTime > $timePeriod) {
                        // Delete the file
                        unlink($filePath);
                        echo "Deleted: $filePath\n";
                    }
                }
            }
        }
        // Close the directory
        closedir($handle);
    }
}

// Path to the root directory
$rootDirectory = '/var/www/source-emr-labalhadi/storage/app/7mJ~33/fqrcde';

// Time period in seconds (24 hours = 86400 seconds)
$timePeriod = 96 * 60 * 60;

// Start the deletion process
deleteExpiredFiles($rootDirectory, $timePeriod);
?>
