<?php

namespace Rmanara\Lib;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Description of DataBaseManager
 *
 * @author Rodrigo
 */
trait DataBaseManager {

    /**
     * 
     * @return \mysqli
     */
    private function db_connection() {

        $dbhost = DB_HOSTNAME;
        $dbuser = DB_USERNAME;
        $dbpass = DB_PASSWORD;
        $dbname = DB_DATABASE;

        return new \mysqli($dbhost, $dbuser, $dbpass, $dbname, 3306);
    }
    /**
     * 
     * @param type $path
     * @param type $input
     * @param type $output
     */
    private function askDump($path, $input, $output) {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Do you need to dump all tables (y/n): ', false);

        if (!$helper->ask($input, $output, $question)) {

            $bundles = $this->list_tables();
            $table_question = new Question('Please enter database name <info> (arrow up or down to select from the list)</info>: ');
            $table_question->setAutocompleterValues($bundles);

            $table_question->setValidator(function ($value) {
                if (trim($value) == '') {
                    throw new \Exception('cannot be empty');
                } elseif (!in_array($value, $this->list_tables())) {
                    throw new \Exception('table does not exist');
                }
                return $value;
            });

            $table = $helper->ask($input, $output, $table_question);
            $this->dumpData($path, $table, $output);
        } else {
            $this->dumpData($path, "*", $output);
        }
    }
    /**
     * 
     * @param OutputInterface $output
     */
    public function optimiseDB(OutputInterface $output) {
        $con = $this->db_connection();
        $query = $con->query("show tables");

        while ($row = $query->fetch_array()) {
            $result = $con->query("OPTIMIZE TABLE {$row[0]};");

            $text = '';
            foreach ($result->fetch_assoc() as $key => $res) {
                $text .= "{$key}:<info>$res</info>; ";
            }
            $output->writeln("$text");
            unset($text);
        }
        $con->close();
    }
    /**
     * 
     * @param type $con
     * @param type $query
     * @return boolean
     * @throws \Exception
     */
    private function checkQuery($con, $query) {

        $result = $con->query('EXPLAIN ' . $query);
        $p = !$result ? true : false;
        if ($p) {
            throw new \Exception($con->error);
        }

        return true;
    }

    /**
     * 
     * @param type $path
     * @param type $name
     * @param OutputInterface $output
     */
    public function dumpData($path, $name, OutputInterface $output) {


        $output->writeln("<info> Start </info>");
        $con = $this->db_connection();

        $tables = [];
        if ($name == '*') {
            $tables = $this->list_tables();
            $name = 'all';
        } else {
            $tables[] = $name;
        }

        $return = '';

        //cycle through
        foreach ($tables as $table) {

            $dump_table = $con->query('SHOW CREATE TABLE ' . $table);
            $dump_table = $dump_table->fetch_array();

            $return .= "-- dump {$table} ;" . PHP_EOL;
            $return .= "DROP TABLE {$table} ;" . PHP_EOL;
            $return .= "$dump_table[1];" . PHP_EOL;

            $getResult = $con->query("select * from $table");

            $total = 0;
            while ($row = $getResult->fetch_assoc()) {
                $list = [];
                foreach ($row as $key => $value) {
                    $list[] = (empty($value) ? "''" : "'" . $con->real_escape_string($value) . "'");

                    $total++;
                }
                $sql = "INSERT INTO {$table} VALUES ( " . implode(",", $list) . " );";

                if ($this->checkQuery($con, $sql)) {
                    $return .= $sql . PHP_EOL;
                }
            }

            $strTotal = "<error> no data found</error>";
            if ($total > 0) {
                $strTotal = "total saved data <error> $total </error>";
            }

            $output->writeln("$table -> <info>  done  </info> $strTotal");
            $return .= "-- end dump {$table} ;" . PHP_EOL;
            $return .= "-- ######## --" . PHP_EOL;
            $return .= "-- ######## --" . PHP_EOL;
        }

        $handle = fopen($path . 'db-backup-' . time() . '-' . $name . '.sql', 'w+');
        fwrite($handle, $return);
        fclose($handle);



        $output->writeln("<info> end </info>");
    }

    /**
     * 
     * @return type
     */
    public function list_tables() {
        $con = $this->db_connection();
        $query = $con->query("show tables");

        $tables = [];
        while ($row = $query->fetch_array()) {
            $tables[] = $row[0];
        }
        return $tables;
    }

}
