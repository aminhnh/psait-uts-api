<?php
require_once "api-config.php";
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'GET':
        if(!empty($_GET["nim"]) && !empty($_GET["kode_mk"])) {
            $nim = filter_input(INPUT_GET, 'nim', FILTER_UNSAFE_RAW);
            $kode_mk = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['kode_mk']);
            get_nilai($nim, $kode_mk);
        } else if(!empty($_GET["nim"])) {
            $nim = filter_input(INPUT_GET, 'nim', FILTER_UNSAFE_RAW);
            get_mahasiswa($nim);
        } else {
            get_all_mahasiswa();
        }
        break;
    case 'POST':
        if (!empty($_GET["nim"]) && !empty($_GET["kode_mk"])) {
            $nim = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['nim']);
            $kode_mk = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['kode_mk']);
            $nilai = filter_var($_POST['nilai'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
            update_nilai($nim, $kode_mk, $nilai);
        } else {
            $nim = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['nim']);
            $kode_mk = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['kode_mk']);
            $nilai = filter_var($_POST['nilai'], FILTER_SANITIZE_NUMBER_INT);
            insert_nilai($nim, $kode_mk, $nilai);
        }
        break;
    case 'DELETE':
        $nim = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['nim']);
        $kode_mk = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['kode_mk']);
        delete_nilai($nim, $kode_mk);
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
            mhs.nim,
            mhs.nama,
            mhs.alamat,
            mhs.tanggal_lahir,
            mk.kode_mk,
            mk.nama_mk,
            mk.sks,
            p.nilai
        FROM 
            perkuliahan p
        INNER JOIN 
            mahasiswa mhs ON p.nim = mhs.nim
        INNER JOIN 
            matakuliah mk ON p.kode_mk = mk.kode_mk;
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

function get_mahasiswa($nim)
{
    global $mysqli;
    $query = "
        SELECT 
            mhs.nim,
            mhs.nama,
            mhs.alamat,
            mhs.tanggal_lahir,
            mk.kode_mk,
            mk.nama_mk,
            mk.sks,
            p.nilai
        FROM 
            perkuliahan p
        INNER JOIN 
            mahasiswa mhs ON p.nim = mhs.nim
        INNER JOIN 
            matakuliah mk ON p.kode_mk = mk.kode_mk
        WHERE
            mhs.nim = ?;
    ";
    $data=array();
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('s', $nim);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while($row=mysqli_fetch_object($result)) {
                $data[]=$row;
            }
        }
        $response=array(
            'status' => 1,
            'message' =>'Get Mahasiswa Successfully.',
            'data' => $data
        );
        $stmt->close();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function get_nilai($nim, $kode_mk)
{
    global $mysqli;
    $query = "
        SELECT 
            *
        FROM 
            perkuliahan
        WHERE
            nim = ? AND
            kode_mk = ?;
    ";
    $data=array();
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('ss', $nim, $kode_mk);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while($row=mysqli_fetch_object($result)) {
                $data[]=$row;
            }
        }
        $response=array(
            'status' => 1,
            'message' =>'Get Nilai Successfully.',
            'data' => $data
        );
        $stmt->close();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function insert_nilai($nim, $kode_mk, $nilai)
{
    global $mysqli;
    $query = "INSERT INTO perkuliahan (nim, kode_mk, nilai) VALUES (?, ?, ?)";

    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('ssd', $nim, $kode_mk, $nilai);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = array(
                    'status' => 1,
                    'message' => 'Nilai added successfully.'
                );
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'Nilai insertion failed.'
                );
            }
            $stmt->close();
        } else {
            $response = array(
                'status' => 0,
                'message' => 'Error preparing the insertion statement.'
            );
        }
    } else {
        $response = array(
            'status' => 0,
            'message' => 'Parameters do not match.'
        );
    }

    // Send response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}


function update_nilai($nim, $kode_mk, $nilai) {
    global $mysqli;
    $sql = "UPDATE perkuliahan SET nilai = ? WHERE nim = ? AND kode_mk = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param('dss', $nilai, $nim, $kode_mk);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = array(
                    'status' => 1,
                    'message' => 'Nilai updated successfully.'
                );
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'No change in nilai or no such record.'
                );
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'Execute failed: ' . $stmt->error
            );
        }

        $stmt->close();
    } else {
        $response = array(
            'status' => 0,
            'message' => 'Prepare failed: ' . $mysqli->error
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}


function delete_nilai($nim, $kode_mk) {
    global $mysqli;
    $query = "DELETE FROM perkuliahan WHERE nim = ? AND kode_mk = ?";
    $stmt = $mysqli->prepare($query);

    if (!$stmt) {
        $response = array(
            'status' => 0,
            'message' => 'Failed to prepare statement: ' . $mysqli->error
        );
    } else {
        $stmt->bind_param("ss", $nim, $kode_mk);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = array(
                    'status' => 1,
                    'message' => 'Nilai deleted successfully.'
                );
            } else {
                $response = array(
                    'status' => 0,
                    'message' => 'No rows affected. Ensure the NIM and Kode MK are correct.'
                );
            }
        } else {
            $response = array(
                'status' => 0,
                'message' => 'Nilai deletion failed: ' . $stmt->error
            );
        }

        $stmt->close();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
?> 