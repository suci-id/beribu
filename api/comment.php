<?php
    include 'db.php';  

    header("Access-Control-Allow-Origin: https://suci-id.github.io"); // Allow specific origin
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow specific HTTP methods
    header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow custom headers
    header("Access-Control-Allow-Credentials: true"); // If you need credentials
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(204); // No Content for preflight
        exit;
    }  

    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($_POST['status']=='get_comments'){
            $id_article = $_POST['id_article'];
            $stmt = $pdo->prepare("SELECT * FROM komentar WHERE id_article = :id_article");
            $stmt->bindParam(':id_article', $id_article);
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result['status'] = 200;
            $result['message'] = "Status komentar berhasil di ambil";
            
            // Convert the array of objects to the desired object format
            foreach ($user as $item) {
                $result['data'][] = (object) $item; // Convert each item to object
            }

            // Convert result array to JSON with desired format (objects inside objects)
            echo json_encode($result, JSON_PRETTY_PRINT);

        }
        if($_POST['status']=='set_comment'){
            $nama = $_POST['nama'];
            $komentar = $_POST['komentar'];
            $id_article = $_POST['id_article'];

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($nama != '' && $komentar != '' && $id_article){
                try{
                    $stmt = $pdo->prepare("INSERT INTO komentar (nama, komentar, date, gambar, id_article) VALUES (:nama, :komentar, CURRENT_TIMESTAMP, :gambar, :id_article)");
                    $stmt->execute([
                        'nama' => $nama,
                        'komentar' => $komentar,
                        'gambar' => '',
                        'id_article' => $id_article
                    ]);
                    $result['status'] = 200;
                    $result['message'] = "Status komentar berhasil";
                } catch(PDOException $e){
                    $result['status'] = 501;
                    $result['message'] = "Status komentar tidak berhasil";
                }
            }
            else{
                $result['status'] = 500;
                $result['message'] = "Status komentar tidak lengkap";
            }
            echo json_encode($result);
        }
    }