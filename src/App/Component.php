<?php

namespace Rmanara\App;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

trait Component {

    protected $path;
    private $directory_count = 0, $file_count = 0;

    /**
     * 
     * @param type $path
     */
    public function __setPath($path) {
        $this->path = $this->getDir($path);
    }

    /**
     * 
     * @return type
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * 
     * @param type $config
     */
    public function pathBuilder($config) {

        foreach ($config as $key => $paths) {
            foreach ($paths as $file) {
                $path_file = str_replace("\\", DIRECTORY_SEPARATOR, $file);
                $final_path = __DIR__ . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . $path_file;
                $this->mkdirRecursive($final_path);
            }
        }
    }

    /**
     * 
     * @param type $path
     */
    public function mkdirRecursive($path) {


        $str = explode(DIRECTORY_SEPARATOR, $path);
        $dir = '';
        foreach ($str as $part) {
            $dir .= DIRECTORY_SEPARATOR . $part;
            if (!is_dir($dir) && strlen($dir) > 0 && strpos($dir, ".") == false) {
                mkdir($dir, 655);
            } elseif (!file_exists($dir) && strpos($dir, ".") !== false) {
                touch($dir);
            }
        }
    }

    /**
     * 
     * @param type $data
     * @param type $search
     * @param type $replace
     * @return type
     */
    public function mappingReplace($data, $search, $replace) {

        $mapping = [];
        foreach ($data as $key => $array) {

            array_walk($array, function (&$v) use ($search, $replace) {
                $v = str_replace($search, $replace, $v);
            });
            $mapping[$key] = $array;
        }
        return $mapping;
    }

    /**
     * 
     * @return array
     */
    public function loadYaml() {
        $file = __DIR__ . DIRECTORY_SEPARATOR . "../settings/config.yml";
        $yaml = new Yaml();
        $data = $yaml->parse(file_get_contents($file));
        return $data;
    }

    /**
     * 
     * @param type $dir
     * @return type
     */
    private function getDir($dir) {

        preg_match("/(.*?)upload/", $dir, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }
        return $dir;
    }

  

   
    


}
