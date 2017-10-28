<?php

namespace Rmanara\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class CommandBuild extends Command {

    //uses as an includo to void code duplication
    use Component;

    /**
     * configuration, setting the commands
     */
    protected function configure() {
        $this
                ->setName('app:build')
                ->setDescription('extension build')

        ;
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln("<comment>Please choose carefully </comment>");
        //choose the method that will be doing // as module or extension
        $method = $this->choose_method($input, $output);
        //check what type the extension 
        $choose_type = $this->choose_type_of_extension($input, $output, $method);
        //choose the file name
        $file = $this->choose_file_name($input, $output, $method);
        //choose the locale for translation
        $locale = $this->askLocale($input, $output);
        //choose the locale for translation
        $version = $this->askOcVersion($input, $output);

        $paths = $this->loadYaml();


        if (!isset($paths['oc_patten'][$version][$method])) {
            $output->writeln("<error>this version $version  not configure </error>");
            $version = $this->askOcVersion($input, $output);
        }

        $dt = $paths['oc_patten'][$version][$method];
        $dt = $this->mappingReplace($dt, '{file}', $file);
        $dt = $this->mappingReplace($dt, '{type}', $choose_type);
        $dt = $this->mappingReplace($dt, '{iso}', $locale);
        $dt = $this->mappingReplace($dt, '{theme-name}', 'default');


        $this->pathBuilder($dt);
        $output->writeln("<info>build complete </info>");
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return type
     */
    public function askOcVersion(InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');
        $question = new Question('Please type the correct oc version:');

        $question->setValidator(function($answer) {
            if (!preg_match("/[a-zA-Z]{1}_[0-9]{1}/i", $answer)) {
                throw new \RuntimeException('please add a valide oc version format: "v_3"');
            }
            return $answer;
        });

        $question->setValidator(function($answer) {
            $paths = $this->loadYaml();
            if (!isset($paths['oc_patten'][$answer])) {
                throw new \RuntimeException("this version $answer  not configure");
            }
            return $answer;
        });


        return $helper->ask($input, $output, $question);
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return type
     */
    public function askLocale(InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');
        $question = new Question('Please type the correct locale for OC:');

        $question->setValidator(function($answer) {
            if (!preg_match("/[a-zA-Z]{2}-[a-zA-Z]{2}/i", $answer)) {
                throw new \RuntimeException('please add a valide locale format: "en-gb"');
            }
            return $answer;
        });


        return $helper->ask($input, $output, $question);
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function choose_type_of_extension(InputInterface $input, OutputInterface $output, $type) {

        $helper = $this->getHelper('question');

        if ($type == 'extension') {
            $option = array('analytics', 'captcha', 'dashboard', 'feed', 'fraud', 'openbay', 'payment', 'report', 'shipping', 'theme', 'total');
            $question = new ChoiceQuestion('Please select', $option, 0);
            $question->setErrorMessage('oi! method %s is invalid.');
            return $helper->ask($input, $output, $question);
        } else {
            return null;
        }
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function choose_method(InputInterface $input, OutputInterface $output) {

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Please select', array('extension', 'module'), 0);
        $question->setErrorMessage('oi! method %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param type $path
     * @return boolean
     */
    private function ask_path_is_correct(InputInterface $input, OutputInterface $output, $path) {

        $helper = $this->getHelper('question');
        $confirmation = new ConfirmationQuestion("all files will be create using this name <info>`$method`</info>, still want to Continue with this action?", false, '/^(y|j)/i');
        if ($helper->ask($input, $output, $confirmation)) {
            return true;
        }
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param type $method
     */
    private function choose_file_name(InputInterface $input, OutputInterface $output, $method) {
        $helper = $this->getHelper('question');
        $question = new Question("Please type <info> $method </info> name: ", "new_file");
        return $helper->ask($input, $output, $question);
    }

}
