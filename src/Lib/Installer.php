<?php

namespace Rmanara\Lib;

use Composer\Script\Event;

/**
 * Description of Installer
 *
 * @author Rodrigo Manara <me@rodrigomanara.co.uk>
 */
class Installer    {
    
    use \Rmanara\App\Component;
    
    public static function Init(Event $event) {

        $path = __DIR__;
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $root = self::getDir($path);
     
        copy($vendorDir .DIRECTORY_SEPARATOR. "rmanara".DIRECTORY_SEPARATOR."App".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."console", $root .DIRECTORY_SEPARATOR. "console");
       
        print_r("file ready") ;
        
    }

   

}
