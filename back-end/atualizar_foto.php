<?php
    session_start();
    require_once 'conexao.php';

    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit;
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
        echo "
        <script>
            alert('Erro ao enviar a imagem.');
            window.location='perfil.php';
        </script>
        ";
        exit;
    }

    $arquivo = $_FILES['foto'];

    // limita o tamanho da imagem para 2mb
    $limite = 2 * 1024 * 1024;
    if ($arquivo['size'] > $limite) {
        echo "
        <script>
            alert('A imagem deve ter no máximo 2MB.');
            window.location='perfil.php';
        </script>
        ";
        exit;
    }

    /* VERIFICA SE É IMAGEM */
    if (!getimagesize($arquivo['tmp_name'])) {
        echo "
        <script>
            alert('O arquivo enviado não é uma imagem válida.');
            window.location='perfil.php';
        </script>
        ";
        exit;
    }

    $extensao = strtolower(
        pathinfo($arquivo['name'], PATHINFO_EXTENSION)
    );

    $permitidas = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    if (!in_array($extensao, $permitidas)) {
        echo "
        <script>
            alert('Formato não permitido. Utilize JPG, JPEG, PNG ou WEBP.');
            window.location='perfil.php';
        </script>
        ";
        exit;
    }

    try {
        /* BUSCA FOTO ANTIGA */
        $stmt = $pdo->prepare("
            SELECT foto
            FROM usuarios
            WHERE id = ?
        ");

        $stmt->execute([
            $_SESSION['id']
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        /* GERA NOVO NOME */
        $novoNome =
            "perfil_" .
            $_SESSION['id'] .
            "_" .
            time() .
            "." .
            $extensao;

        $caminho = "uploads/perfis/" . $novoNome;
        if (!move_uploaded_file(
            $arquivo['tmp_name'],
            $caminho
        )) {

            throw new Exception(
                "Falha ao mover arquivo."
            );
        }

        /* REMOVE FOTO ANTIGA */

        if (
            !empty($usuario['foto']) &&
            file_exists($usuario['foto'])
        ) {

            unlink($usuario['foto']);
        }

        /* ATUALIZA BANCO */
        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET foto = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $caminho,
            $_SESSION['id']
        ]);

        // Atualiza a sessão para refletir a nova foto
        $_SESSION['foto'] = $caminho;

        header("Location: perfil.php");
        exit;

        // Especifica melhor o erro, sem uso de alert
        } catch (Exception $e) {

            echo "<pre>";
            echo $e->getMessage();
            echo "</pre>";
        }
    ?>