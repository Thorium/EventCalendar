<?php
////////////////////////////////////////////////////////////////////////////////
//
// Tiedosto: Data_Access.php
//
// Kuvaus:   Tää on niinkuin mun tietokantakerros tai silleen...
//
// Versiot:  16.6.2005 - Tuomas Hietanen - Ensimmäinen versio
//           19.5.2013 - Tuomas Hietanen - SQL to SQLi
//
////////////////////////////////////////////////////////////////////////////////

class Data_Access {

    /* Avaa tietokantayhteyden */
    function openDatabase() {
        global $dataconnection1;
        // Haetaan tietokannan asetukset
        // require '/var/www/localhost/sql_connect/eventcalendar.inc';
        $this->server = "localhost"; // $db_server1;
        $this->databasename =  "eventcalendar"; // = $db_name1;
        $this->user =  "eventcalendar"; // $db_user1;
        $this->pwd = "cal123"; //$db_pass1;

        $this->connection = mysqli_connect($this->server,$this->user,$this->pwd,$this->databasename);
        if (mysqli_connect_errno($this->connection)) {
            echo mysqli_connect_error() . "\n<br><br>";
            mysqli_close($this->connection);
            exit("Can't connect to server.");
        }
        
        // register_shutdown_function($this->closeDatabase);
    }

    /* Sulkee tietokantayhteyden */
    function closeDatabase() {
        // näitä kutsutaan automaagisesti, joten ei tarvita.
        // mysqli_free_result();
        // mysqli_close($this->connection);
    }

    /* Hakee sql-kyselyn taulukkoon (esim. SELECT) */
    /* Parametrit: SQL-lause */
    /* Palautus: SQL-lauseen tulos */
    function getValues($sql_query){
        if(func_num_args()<1){
          echo("SQL-Query is missing!\n");
          return 99;
        }
        $q_result=mysqli_query($this->connection,$sql_query);
        if (!$q_result) {
            echo mysqli_errno() . ": " . mysqli_error() . "\n<br><br>";
            echo htmlspecialchars($sql_query);
            exit("\n<br><br>Query failed.");
        }
        return $q_result;
    }


    /* Hakee yksittäisen arvon (esim. SELECT) */
    /* Parametrit: SQL-lause, palautettavan tietokentän nimi */
    /* Palautus:  palautettavan tietokentän arvo*/
    function getValue($sql_query, $field_name){
        if(func_num_args()<1){
          echo("SQL-Query is missing!\n");
          return 99;
        }
        $q_result=mysqli_query($this->connection,$sql_query);
        if (!$q_result) {
            echo mysqli_errno() . ": " . mysqli_error() . "\n<br><br>";
            echo htmlspecialchars($sql_query);
            exit("\n<br><br>Query failed.");
        }
        $return_value = mysqli_result($q_result, 0, $field_name);
        mysqli_free_result($q_result);
        return $return_value;

    }

    /* Lisää rivin ja palauttaa lisätyn rivin ID:n */
    /* Parametrit: SQL-lause */
    /* Palautus: Lisätyn rivin ID-tunnus */
    function insertRow($sql_query){
        if(func_num_args()<1){
          echo("SQL-Query is missing!\n");
          return 99;
        }
        $q_result=mysqli_query($this->connection,$sql_query);
        if(!$q_result){
            echo mysqli_errno() . ": " . mysqli_error() . "\n<br><br>";
            echo htmlspecialchars($sql_query);
            exit("\n<br><br>Query failed.");
        }
        return mysqli_insert_id();
    }

    /* Suorittaa SQL-lauseen (esim. UPDATE, DELETE tai REPLACE) */
    /* Parametrit: SQL-lause */
    /* Palautus: Ei palauta arvoja */
    function executeSQL($sql_query){
        if(func_num_args()<1){
          echo("SQL-Query is missing!\n");
          return 99;
        }
        $q_result=mysqli_query($this->connection,$sql_query);
        if (!$q_result) {
            echo mysqli_errno() . ": " . mysqli_error() . "\n<br><br>";
            echo htmlspecialchars($sql_query);
            exit("\n<br><br>Query failed.");
        }
        return 0;
    }

}
?>
