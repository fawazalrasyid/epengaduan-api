<?php

include "config/connection.php";

header('Content-Type: application/json');

$method  = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);
parse_str(isset($url_components['query']) ? ($url_components['query']) : NULL, $params);
$p_nik = isset($params['nik']) ? ($params['nik']) : NULL;
$p_id = isset($params['id_pengaduan']) ? ($params['id_pengaduan']) : NULL;
$p_status = isset($params['status']) ? ($params['status']) : NULL;

if ($method == 'GET') {

    if ($p_nik && $p_id != NULL) {
        $sql = "SELECT p.id_pengaduan, p.nik, p.tgl_pengaduan, p.isi_laporan, t.tgl_tanggapan, t.tanggapan, p.status FROM pengaduan AS p LEFT JOIN tanggapan AS t ON p.id_pengaduan = t.id_pengaduan WHERE p.nik = $p_nik AND p.id_pengaduan = $p_id ORDER BY p.id_pengaduan ASC";
    } elseif ($p_nik && $p_status != NULL) {
        $sql = "SELECT p.id_pengaduan, p.nik, p.tgl_pengaduan, p.isi_laporan, t.tgl_tanggapan, t.tanggapan, p.status FROM pengaduan AS p LEFT JOIN tanggapan AS t ON p.id_pengaduan = t.id_pengaduan WHERE p.nik = $p_nik AND p.status = '$p_status' ORDER BY p.id_pengaduan ASC";
    } elseif ($p_id != NULL) {
        $sql = "SELECT p.id_pengaduan, p.nik, p.tgl_pengaduan, p.isi_laporan, t.tgl_tanggapan, t.tanggapan, p.status FROM pengaduan AS p LEFT JOIN tanggapan AS t ON p.id_pengaduan = t.id_pengaduan WHERE p.id_pengaduan = $p_id ORDER BY p.id_pengaduan ASC";
    } elseif ($p_nik != NULL) {
        $sql = "SELECT p.id_pengaduan, p.nik, p.tgl_pengaduan, p.isi_laporan, t.tgl_tanggapan, t.tanggapan, p.status FROM pengaduan AS p LEFT JOIN tanggapan AS t ON p.id_pengaduan = t.id_pengaduan WHERE p.nik = $p_nik ORDER BY p.id_pengaduan ASC";
    } elseif ($p_status != NULL) {
        $sql = "SELECT p.id_pengaduan, p.nik, p.tgl_pengaduan, p.isi_laporan, t.tgl_tanggapan, t.tanggapan, p.status FROM pengaduan AS p LEFT JOIN tanggapan AS t ON p.id_pengaduan = t.id_pengaduan WHERE p.status = '$p_status' ORDER BY p.id_pengaduan ASC";
    } else {
        $sql = "SELECT p.id_pengaduan, p.nik, p.tgl_pengaduan, p.isi_laporan, t.tgl_tanggapan, t.tanggapan, p.status FROM pengaduan AS p LEFT JOIN tanggapan AS t ON p.id_pengaduan = t.id_pengaduan ORDER BY p.id_pengaduan ASC";
    }

    $query = mysqli_query($conn, $sql);

    $item = array();

    while ($data = mysqli_fetch_array($query)) {

        // $item[] = array(
        //     intval($data["id_pengaduan"]) => array(
        //         'nik' => intval($data["nik"]),
        //         'tgl_pengaduan' =>  date('Y-m-d H:i:s', strtotime($data["tgl_pengaduan"])),
        //         'laporan' => $data["isi_laporan"],
        //         'tgl_tanggapan' => !empty($data["tgl_tanggapan"]) ? date('Y-m-d H:i:s', strtotime($data["tgl_tanggapan"])) : NULL,
        //         'tanggapan' => !empty($data["tanggapan"]) ? $data["tanggapan"] : NULL,
        //         'status' => $data["status"],
        //     ),
        // );

        $item[] = array(
            'id_pengaduan' => intval($data["id_pengaduan"]),
            'nik' => intval($data["nik"]),
            'tgl_pengaduan' =>  date('Y-m-d H:i:s', strtotime($data["tgl_pengaduan"])),
            'laporan' => $data["isi_laporan"],
            'tgl_tanggapan' => !empty($data["tgl_tanggapan"]) ? date('Y-m-d H:i:s', strtotime($data["tgl_tanggapan"])) : NULL,
            'tanggapan' => !empty($data["tanggapan"]) ? $data["tanggapan"] : NULL,
            'status' => $data["status"],
        );
    }

    if ($item == NULL) {
        $response = array(
            'status_code' => 401,
            'message' => 'data tidak ditemukan',
            'data' => $item
        );
    } else {
        $response = array(
            'status_code' => 200,
            'message' => 'sukses menarik data pengaduan',
            'data' => $item
        );
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
} elseif ($method == 'POST') {

    if (isset($_POST['nik']) && isset($_POST['tgl_pengaduan']) && isset($_POST['isi_laporan'])) {

        $nik = $_POST['nik'];
        $tgl_pengaduan =  $_POST['tgl_pengaduan'];
        $isi_laporan = $_POST['isi_laporan'];


        $query = "INSERT INTO pengaduan(tgl_pengaduan, nik, isi_laporan) VALUES('$tgl_pengaduan', '$nik', '$isi_laporan')";
        $pengaduan = mysqli_query($conn, $query);

        if ($pengaduan) {

            $item = array(
                'nik' => $nik,
                'tgl_pengaduan' => $tgl_pengaduan,
                'isi_laporan' => $isi_laporan
            );

            $response = array(
                'status_code' => 200,
                'message' => 'Sukses menambahkan data pengaduan',
                //'data' => $item
            );

            echo json_encode($response, JSON_PRETTY_PRINT);
        } else {

            $item = array();

            $response = array(
                'status_code' => 401,
                'message' => 'Terjadi kesalahan saat melakukan registrasi',
                'data' => $item
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
        'message' => 'method is not supported for this route. Supported methods: GET, POST'
    );

    echo json_encode($response, JSON_PRETTY_PRINT);
}
