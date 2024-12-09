<?php
    include 'db.php';  

    // username : suci.id
    // password : 11223344

    $result = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($_POST['status_auth'] == 'login'){
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();
            // echo $user . "<br>";
    
            if($username == $user['username'] && password_verify($password, $user['password'])){
                // echo "successfully";
                $result['status'] = 200;
                $result['message'] = "Status Successfully Login";
                $result['username'] = $user['username'];
            }
            else{
                $result['status'] = 200;
                $result['message'] = "Status Failed Login";
            }
            echo json_encode($result);
            return json_encode($result);
        }
        else if ($_POST['status_auth']=='register'){
            $username = $_POST['username'];
            $password = $_POST['password'];

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($username != '' && $password != ''){
                try{
                    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                    $stmt->execute([
                        'username' => $username,
                        'password' => $hashedPassword,
                    ]);
                    $result['status'] = 200;
                    $result['message'] = "Status pendaftaran berhasil";
                } catch(PDOException $e){
                    $result['status'] = 200;
                    $result['message'] = "Status pendaftaran tidak berhasil";
                }
            }
            else{
                $result['status'] = 200;
                $result['message'] = "Status pendaftaran tidak lengkap";
            }
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