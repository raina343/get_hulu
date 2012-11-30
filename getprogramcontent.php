<?
include ("includes/config.php");
include ("includes/functions.php");
if ($_GET['gettable']=="true"){
// This is where we get the PCR List.
//$aColumns = array( 'id2','id','PCR','Incident', 'PatientName', 'Address', 'Status','DateCreated','Report','Report2','id3','id4');
	$aColumns = array( 'id2','id','Actions','Image', 'Type', 'Name', 'Season','Episode','Description','Categories','DateAdded','EpisodeName');
	$dbQuery = "SELECT id as id2,id as id,HuluID as Actions,content_id as Image,";
	$dbQuery .="is_subscriber_only as Type,show_name as Name,";
	$dbQuery .="season_number as Season,CONCAT (episode_number, ' - ', title) as Episode,description as Description,";
	$dbQuery .="show_genres as Categories,DateAdded as DateAdded, title as EpisodeName,id as id4";
	$dbQuery .=" FROM ProgramData WHERE is_subscriber_only='0' AND expires_at >'".date(c)."' OR expires_at='0000-00-00'";
//	echo $dbQuery;	
	$result = mysql_query($dbQuery) or die (mysql_error());
        $output = array("aaData" => array());
        while ( $aRow = mysql_fetch_array( $result ) ){
                $row = array();
                for ( $i=0 ; $i<count($aColumns) ; $i++ ){
//                        if (($i==7)||($i==8)){
//                                if ($i==7){
//                                        $row[] = mysqldate($aRow[ $aColumns[$i] ]);
//                                }else {
//                                        $row[] = "1";
//                                }

//                        }else {
                        $row[] = $aRow[ $aColumns[$i] ];
//                        }
                }
                $output['aaData'][] = $row;
        }
       echo json_encode( $output );
}
