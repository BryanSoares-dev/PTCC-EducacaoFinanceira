<?php
    session_start();
    require_once 'conexao.php'; // aqui a variável disponível é $pdo, não $conn
    require_once 'google-config.php';

    // colocando informações de um novo cliente no objeto da biblioteca do google 
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);       // era setClienteId (nome errado)
    $client->setClientSecret(GOOGLE_CLIENT_SECRET); // era setClienteSecrete (nome errado)
    $client->setRedirectUri(GOOGLE_REDIRECT_URL); //
    $client->addScope('email');
    $client->addScope('profile');

    // caso ainda não tenha o code, o usuário será direcionado a outra página de autenticação para receber o code único do google
    if (!isset($_GET['code'])) {
        $auth_url = $client->createAuthUrl();
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        exit;

    } else {
        // Troca o "code" recebido do Google (via URL, após o usuário autorizar o app)
        // por um token de acesso válido
        $client->authenticate($_GET['code']);

        // Obtém o token de acesso gerado após a autenticação
        $token = $client->getAccessToken();

        // Define/reaplica o token de acesso no client,
        // garantindo que ele esteja autenticado para futuras chamadas à API
        $client->setAccessToken($token);

        // Cria uma instância do serviço OAuth2 do Google, usando o client autenticado
        $oauth = new Google_Service_Oauth2($client);

        // Faz a chamada à API para buscar os dados do usuário logado
        // (nome, e-mail, foto de perfil, ID do Google, etc.)
        $userInfo = $oauth->userinfo->get();
    }

    // Salvando os dados no banco de dados usando PDO

    // Verifica se o usuário já existe pelo e-mail
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$userInfo->email]);
    $usuarioExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioExistente) {
        // Usuário realmente novo: insere no banco
        $stmt = $pdo->prepare("INSERT INTO usuarios (oauth_uid, nome, email, foto) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userInfo->id, $userInfo->name, $userInfo->email, $userInfo->picture]);
        $idUsuario = $pdo->lastInsertId();

    } else {
        // Usuário já existe (cadastro antigo): associa o oauth_uid a essa conta, se ainda não tiver
        if (empty($usuarioExistente['oauth_uid'])) {
            $stmt = $pdo->prepare("UPDATE usuarios SET oauth_uid = ? WHERE id = ?");
            $stmt->execute([$userInfo->id, $usuarioExistente['id']]);
        }
        $idUsuario = $usuarioExistente['id'];
    }

    // Salva os dados do usuário logado na sessão
    $_SESSION['id'] = $idUsuario;
    $_SESSION['usuario'] = [
        'nome'  => $userInfo->name,
        'email' => $userInfo->email,
        'foto'  => $userInfo->picture
    ];

    header('Location: ../front-end/home.php');
?>    