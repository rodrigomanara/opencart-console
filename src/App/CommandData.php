<?php

namespace Rmanara\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument; 

/**
 * Description of CommandData
 *
 * @author Rodrigo
 */
class CommandData extends Command {

    //put your code here


    use Component;
    use \Rmanara\Lib\DataBaseManager;   
    /**
     * configuration, setting the commands
     */
    protected function configure() {
        $this
                ->setName('app:data')
                ->addArgument('method', InputArgument::REQUIRED, 'the method used to manage the data {dump, optimise}')
                ->setDescription('manage data')


        ;
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output) {

        $path = $this->getPath();

        $type = $input->getArgument('method');
        $output->writeln("<info> start $type</info>");
        switch ($type) {
            case 'dump' : $this->askDump($path, $input, $output);
                break;
            case 'optimise' : $this->optimiseDB($output);
                break;
            
            case 'user' : "";break;
        }
    }

   

}
