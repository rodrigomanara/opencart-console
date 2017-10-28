<?php

namespace Rmanara\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;
use Comodojo\Zip\Zip;// need to find a better way to zip a multiple folder and file

class CommandExport extends Command {

    
    //use to void duplication of code also not able to extends Symfony\Component\Console\Command\Command 
    use Component;
   
    /**
     * configuration, setting the commands
     */
    protected function configure() {
        $this
                ->setName('app:export')
                ->setDescription('export and zipped files to opencart extensions models')
                ->addArgument('filename', InputArgument::REQUIRED, 'find all files begin|has with that name')

        ;
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $name = $input->getArgument('filename');
        $dir = $this->path . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR;

        $finder = new Finder();
        $finder->files()->in($dir);
        $finder->files()->name("*$name*");

        $output->writeln("<comment>===== start +++</comment>");

        foreach ($finder as $file) {
            $filename = $file->getfilename();
            $path = $file->getRelativePath();

            if ($this->builder($path, $file->getRealPath(), $filename, $output , $name)) {
                $this->file_count++;
                $output->writeln("<info>file $filename was copied to $path</info>");

            }
        }
        $output->writeln("<comment>files copied = (". $this->file_count .")</comment>");
        $output->writeln("<comment>directories created = (". $this->directory_count .")</comment>");
    
        
        $output->writeln("<comment>===== end +++</comment>");
    }
    

    /**
     * 
     * @param type $path
     * @param type $copy
     * @param type $filename
     * @param OutputInterface $output
     * @return type
     */
    private function Builder($path, $copy, $filename, OutputInterface $output , $name) {
        $temp = $this->path . DIRECTORY_SEPARATOR . "temp_extension" . DIRECTORY_SEPARATOR . $name .  DIRECTORY_SEPARATOR. "upload" . DIRECTORY_SEPARATOR . $path;

        if (!is_dir($temp)) {
            if (mkdir($temp, 655, true)) {
                $this->directory_count++;
                $output->writeln("final path $temp");
            }
        }
        $temp_path = $temp . DIRECTORY_SEPARATOR . $filename;
        return copy($copy, $temp_path);
    }

    

}
