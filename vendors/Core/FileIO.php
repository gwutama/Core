<?php

namespace Core;

/**
 * <h1>Class FileIO</h1>
 *
 * <p>
 * This class contains file IO operations.
 * </p>
 */
class FileIO
{
    /**
     * Writes data to a file.
     *
     * @static
     * @param $file     Filename.
     * @param $data     Data to write.
     */
    public static function write($file, $data) {
        $fh = fopen($file, 'w+');
        fwrite($fh, $data);
        fclose($fh);
    }


    /**
     * Reads data from a file.
     *
     * @static
     * @param $file     Filename.
     * @return string
     */
    public static function read($file) {
        $fh = fopen($file, 'r');
        $data = fread($fh, filesize($file));
        fclose($fh);

        return $data;
    }


    /**
     * Deletes a specific file.
     *
     * @static
     * @param $file     Filename.
     * @throws exception
     */
    public static function delete($file) {
        if (!file_exists($file)) {
            throw new \Exception("File $file doesn't exist.");
        }
        else {
            @unlink($file);
        }
    }

}

?>