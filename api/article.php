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
        if($_POST['status']=='get_article'){
            $id_article = $_POST['id_article'];
            $stmt = $pdo->prepare("SELECT * FROM article WHERE id = :id_article");
            $stmt->bindParam(':id_article', $id_article);
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convert the array of objects to the desired object format
            foreach ($user as $item) {
                $result[] = (object) $item; // Convert each item to object
            }

            // Convert result array to JSON with desired format (objects inside objects)
            echo json_encode($result, JSON_PRETTY_PRINT);

        }
        if($_POST['status']=='send_article'){
            $result = [];
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['error' => 'Gagal mengunggah file']);
                exit;
            }
            
        
            // Periksa folder tujuan
            $uploadDir = '../images/article_image/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Buat folder jika belum ada
            }
            
        
            // Simpan file dengan nama unik
            $fileName = uniqid() . '-' . basename($_FILES['image']['name']);
            $filePath = $uploadDir . $fileName;
            
        
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                // Ambil data teks (jika ada)
                $desc = isset($_POST['text']) ? $_POST['text'] : '';
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $kategori= isset($_POST['kategori']) ? $_POST['kategori'] : '';
                $image = $filePath;

                
                // Simpan ke database
                try{
                    $stmt = $pdo->prepare("INSERT INTO article (title, description, thumbnail, kategori, id_user) VALUES (:title, :description, :thumbnail, :kategori, :id_user)");
                    $stmt->execute([
                        'title' => $title,
                        'description' => $desc,
                        'thumbnail' => $image,
                        'kategori' => $kategori,
                        'id_user' => 1,
                    ]);
                    $result['status'] = 200;
                    $result['message'] = "Status artikel berhasil di buat";
                    $result['console'] = [
                        'status' => $_POST['id_article'],
                        'title' => $title,
                        'description' => $desc,
                        'thumbnail' => $image,
                    ];
                } catch(PDOException $e) {
                    $result['status'] = 200;
                    $result['message'] = "Status artikel tidak berhasil di buat";
                    $result['console'] = [
                        'status' => $_POST['id_article'],
                        'title' => $title,
                        'description' => $desc,
                        'thumbnail' => $image,
                    ];
                }
                

                // Kirim respon berhasil
                echo json_encode($result);
            } else {
                echo json_encode(
                    ['error' => 'Gagal menyimpan file'],
                    ['message' => 'Status artikel gagal dibuat'] 
                );
            }
        }
        if($_POST['status']=='update_article'){
            
            $result = [];
            // if($_POST['image'] != 'null-image'){
            if($_FILES['image']){
                if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    echo json_encode(['error' => 'Gagal mengunggah file']);
                    exit;
                }
            
                // Periksa folder tujuan
                $uploadDir = '../images/article_image/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // Buat folder jika belum ada
                }
            
                // Simpan file dengan nama unik
                $fileName = uniqid() . '-' . basename($_FILES['image']['name']);
                $filePath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                    $image = $filePath;
                } else {
                    return json_encode([
                        'error' => 'Gagal menyimpan file',
                        'message' => 'Gagal menyimpan file',
                        'status' => '500'
                    ]);
                }
            } else {
                $image = $_POST['image'];
            }
            
        
            // Ambil data teks (jika ada)
            $desc = isset($_POST['text']) ? $_POST['text'] : '';
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
            // Simpan ke database
            try{
                $stmt = $pdo->prepare("UPDATE article SET title = :title, description = :description, kategori = :cat, thumbnail = :thumbnail WHERE id = :id_article");
                $stmt->execute([
                    'title' => $title,
                    'description' => $desc,
                    'cat' => $kategori,
                    'thumbnail' => $image,
                    'id_article' => $_POST['id_article'],
                ]);
                $result['status'] = 200;
                $result['message'] = "Status artikel berhasil di edit";
                $result['console'] = [
                    'status' => $_POST['status'],
                    'title' => $title,
                    'description' => $desc,
                    'thumbnail' => $_FILES['image'],
                    'id_article' => $_POST['id_article'],
                ];
            } catch(PDOException $e) {
                $result['status'] = 200;
                $result['message'] = "Status artikel tidak berhasil di edit";
                $result['console'] = [
                    'status' => $_POST['status'],
                    'title' => $title,
                    'description' => $desc,
                    'thumbnail' => $_FILES['image'],
                    'id_article' => $_POST['id_article'],
                ];
            }

            // Kirim respon berhasil
            echo json_encode($result);
        }
        if($_POST['status']=='delete_article'){
            $result = [];
            try{
                $id_article = $_POST['id_article'];
                $stmt = $pdo->prepare("DELETE FROM article WHERE id = :id_article");
                // $stmt->bindParam(':id_article', $id_article);
                $stmt->execute([
                    'id_article' => $id_article
                ]);

                $result['status'] = 200;
                $result['message'] = "Status Artikel sudah dihapus";
                $result['console'] = [
                    'status' => $_POST['status'],
                    'id_article' => $_POST['id_article']
                ];
            } catch(PDOException $e){
                $result['status'] = 500;
                $result['message'] = "Status artikel tidak berhasil di hapus, terjadi error";
                $result['error'] = $e;
            }
            
            // $result['console'] = [
            //     'status' => $_POST['status'],
            //     'id_article' => $_POST['id_article']
            // ];

            echo json_encode($result);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET'){
        $stmt = $pdo->prepare("SELECT * FROM article");
        $stmt->execute();
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert the array of objects to the desired object format
        foreach ($user as $item) {
            $result[] = (object) $item; // Convert each item to object
        }

        // Convert result array to JSON with desired format (objects inside objects)
        echo json_encode($result);
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $result = [];
        if($_DELETE['status'] == 'delete_article'){
            try{
                $id_article = $_DELETE['id_article'];
                $stmt = $pdo->prepare("DELETE FROM article WHERE id = :id_article");
                $stmt->bindParam(':id_article', $id_article);
                $stmt->execute();

                $result['status'] = 200;
                $result['message'] = "Status Artikel sudah dihapus";
                $result['console'] = [
                    'status' => $_DELETE['status'],
                    'id_article' => $_DELETE['id_article']
                ];
            } catch(PDOException $e){
                $result['status'] = 500;
                $result['message'] = "Status artikel tidak berhasil di hapus, terjadi error";
                $result['error'] = $e;
            }

            echo json_encode($result);
        }
    }