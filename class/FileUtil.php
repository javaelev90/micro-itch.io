<?php

    class FileUtil {

        private static $PRIVILEGES = 0755;

        // Creates a directory, returns true/false depending on success
        public static function makeDirectory($directoryPath, $recursive) {
            if(!self::doesDirectoryExist($directoryPath)){
                return mkdir($directoryPath, self::$PRIVILEGES, $recursive);
            }
            return false;
        }

        // Checks if directory exist
        public static function doesDirectoryExist($directoryPath) {
            return self::doesFileExist($directoryPath);
        }

        // Checks if file exist
        public static function doesFileExist($filePath) {
            return file_exists($filePath);
        }

        // Deletes directory and all content recursively starting from directoryPath
        public static function deleteDirectoryAndContent($directoryPath){
            self::deleteDirectoryWithContent($directoryPath, true);
        }

        // Deletes directory(besides root) and all content recursively starting from directoryPath
        public static function deleteDirectoryContent($directoryPath){
            self::deleteDirectoryWithContent($directoryPath, false);
        }

        // Deletes directory and all content recursively starting from directoryPath
        // Only deletes root if deleteRootDirectory is true
        private static function deleteDirectoryWithContent($directoryPath, $deleteRootDirectory){
            $items = array_diff(scandir($directoryPath), array('..', '.'));
            foreach ($items as $index => $item) {
                $item = $directoryPath . "/" . $item;
                if (is_dir($item)) {
                    self::deleteDirectoryWithContent($item, true);
                } else {
                    unlink($item);
                }
            }
            if($deleteRootDirectory){
                rmdir($directoryPath);
            }
        }

        // Deletes directory, returns true/false depending on success
        public static function deleteDirectory($directoryPath) {
            // If the directory is empty then delete the directory
            if($directoryPath != "" && count(scandir($directoryPath)) == 2) {
                return rmdir($directoryPath);
            }
            return false;
        }
    
        // Deletes file, returns true/false depending on success
        public static function deleteFile($filePath) {
            // If the path is not empty then delete file
            if($filePath != "") {
                return unlink($filePath);
            }
            return false;
        }

        // Tries to unzips file, return true if successful otherwise it returns false
        public static function unzipFile($zipFilePath, $extractToDirectory) {
            $zip = new ZipArchive();
            if($zip->open($zipFilePath) === TRUE) {
                $result = $zip->extractTo($extractToDirectory);
                $zip->close();
                return $result;
            } 
            return false;
        }

        // F.e. check if the file 'text.txt' has the extension '.txt'
        public static function isFileOfType($filePath, $fileTypeExtensionArray) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            return in_array($extension, $fileTypeExtensionArray);
        }

        // Recursively changes privileges on all files/directories starting from directoryPath
        public static function chmodRecursive($directoryPath) {
            $directory = new DirectoryIterator($directoryPath);
            foreach ($directory as $item) {
                chmod($item->getPathname(), self::$PRIVILEGES);
                if ($item->isDir() && !$item->isDot()) {
                    self::chmodRecursive($item->getPathname());
                }
            }
        }

        // Search for files in a directory that has a certain name pattern
        public static function findFiles($fileDirectory, $filePattern){
            $searchPath = $fileDirectory . "/" . $filePattern;
            return glob($searchPath);
        }

        // Renames file
        public static function renameFile($currentName, $newName) {
            return rename($currentName, $newName);
        }

        // Rename directory
        public static function renameDirectory($currentName, $newName) {
            return self::renameFile($currentName, $newName);
        }

    }

?>