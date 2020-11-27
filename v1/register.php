<?php

include "config/connection.php";

header('Content-Type: application/json');

$method  = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {

    if (isset($_POST['nik']) && isset($_POST['nama']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['telp'])) {

        $nik = $_POST['nik'];
        $nama =  $_POST['nama'];
        $username = $_POST['username'];
        $password =  password_hash($_POST['password'], PASSWORD_DEFAULT);
        $telp = $_POST['telp'];

        $query = "SELECT * FROM masyarakat WHERE masyarakat.username = '$username'";
        $rslt = mysqli_query($conn, $query);
        $check_username = mysqli_num_rows($rslt);

        if ($check_username == 0) {

            $sql = "INSERT INTO masyarakat(nik, nama, username, password, telp) VALUES('$nik', '$nama', '$username', '$password', '$telp')";
            $regist_user = mysqli_query($conn, $sql);

            $item = array();

            if ($regist_user) {

                $usr = "SELECT * FROM masyarakat WHERE masyarakat.username = '$username'";
                $result = mysqli_query($conn, $usr);
                while ($user = mysqli_fetch_array($result)) {

                    $item = array(
                        'nik' => $user["nik"],
                        'nama' => $user["nama"],
                        'username' => $user["username"],
                        'password' => $user["password"],
                        'telp' => $user["telp"],
                        'imagePath' => $user["foto_profile"],
                    );
                }

                $response = array(
                    'status' => 200,
                    'message' => 'sukses menambahkan data user',
                    'data' => $item
                );

                echo json_encode($response, JSON_PRETTY_PRINT);
            } else {
                $response = array(
                    'status' => 401,
                    'message' => 'terjadi kesalahan saat melakukan registrasi',
                    'data' => $item
                );

                echo json_encode($response, JSON_PRETTY_PRINT);
            }
        } else {
            $response = array(
                'status_code' => 422,
                'message' => 'username telah digunakan'
            );

            echo json_encode($response, JSON_PRETTY_PRINT);
        }
    } else {
        $response = array(
            'status_code' => 401,
            'message' => 'null data given'
        );

        echo json_encode($response, JSON_PRETTY_PRINT);
    }
} else {

    $response = array(
        'status_code' => 401,
        'message' => 'method is not supported for this route. Supported methods: POST'
    );

    echo json_encode($response, JSON_PRETTY_PRINT);
}
