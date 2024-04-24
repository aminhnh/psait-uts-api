<?php
require_once "api-config.php";
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
            get_all_mahasiswa();
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_all_mahasiswa()
{
    global $mysqli;
    $data=array();
    $query = "
        SELECT 
            *
        FROM mahasiswa;
    ";
    $result=$mysqli->query($query);
    while($row=mysqli_fetch_object($result)) {
        $data[]=$row;
    }
    $response=array(
                    'status' => 1,
                    'message' =>'Get Mahasiswa List Successfully.',
                    'data' => $data
                );
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>