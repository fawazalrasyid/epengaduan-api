<?php

include "config/connection.php";

header('Content-Type: application/json');

$method  = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {

    if (isset($_POST['username']) && isset($_POST['password'])) {

        $username = $_POST['username'];
        $password =  $_POST['password'];

        $sql = "SELECT * FROM masyarakat WHERE masyarakat.username = '$username' ";
        $query = mysqli_query($conn, $sql);

        $item = array();
        $session = FALSE;
        $hash = '';

        while ($data = mysqli_fetch_array($query)) {

            $item = array(
                'nik' => $data["nik"],
                'nama' => $data["nama"],
                'username' =>  $data["username"],
                'password' => $data["password"],
                'telp' => $data["telp"],
                'imagePath' => !empty($data["foto_profile"]) ? $data["foto_profile"] : "user.png",
            );

            $hash = $data["password"];
        }

        if (password_verify(strval($password), $hash)) {
            $session = TRUE;
        }

        if ($session) {

            $response = array(
                'status_code' => 200,
                'message' => 'login berhasil',
                'data' => $item
            );

            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {

            $response = array(
                'status_code' => 401,
                'message' => 'login gagal. password atau username salah',
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
