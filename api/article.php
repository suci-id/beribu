<?php
    include 'db.php';  

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
                $image = $filePath;
                // Simpan ke database
                try{
                    $stmt = $pdo->prepare("INSERT INTO article (title, description, thumbnail, id_user) VALUES (:title, :description, :thumbnail, :id_user)");
                    $stmt->execute([
                        'title' => $title,
                        'description' => $desc,
                        'thumbnail' => $image,
                        'id_user' => 1,
                    ]);
                    $result['status'] = 200;
                    $result['message'] = "Status artikel berhasil di buat";
                } catch(PDOException $e) {
                    $result['status'] = 200;
                    $result['message'] = "Status artikel tidak berhasil di buat";
                }
                

                // Kirim respon berhasil
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'Gagal menyimpan file']);
            }
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