<?
$dbServer = "localhost";
$dbUser = "root";
$dbPass = "12Fish";
        $dbDatabase = "get_hulu";
        $sConn = mysql_connect($dbServer, $dbUser, $dbPass) or fwrite($logfile,"Couldn't connect to database server"."\n");
        $dConn = mysql_select_db($dbDatabase, $sConn) or fwrite($logfile,"Couldn't connect to database $dbDatabase");
