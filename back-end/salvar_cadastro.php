<?php
require_once __DIR__ . '/seguranca.php';
iniciar_sessao_segura();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF: impede que outro site envie um cadastro/alteração de senha
    // em nome do visitante sem ele perceber.
    csrf_exigir();

    $nome = trim((string) ($_POST['nome'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');
    $confirmar_senha = (string) ($_POST['confirmar_senha'] ?? '');
    $telefone = trim((string) ($_POST['telefone'] ?? ''));

    // ===================== VALIDAÇÃO SERVER-SIDE =====================
    // Problema: o formulário já usa "required"/"type=email" no HTML, mas
    // isso só ajuda a experiência do usuário — qualquer pessoa pode
    // montar e enviar a requisição diretamente (via curl, Postman, etc.)
    // ignorando essas restrições do navegador.
    // Solução: repetir as validações essenciais no servidor.
    if ($nome === '' || mb_strlen($nome) > 150) {
        echo "<script>alert('Informe um nome válido.'); window.history.back();</script>";
        exit;
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
        echo "<script>alert('Informe um e-mail válido.'); window.history.back();</script>";
        exit;
    }

    if ($telefone !== '' && !preg_match('/^[0-9()+\-\s]{8,20}$/', $telefone)) {
        echo "<script>alert('Informe um telefone válido.'); window.history.back();</script>";
        exit;
    }

    // Senha mínima: reduz o risco de contas com senhas triviais que
    // seriam quebradas facilmente mesmo com hashing correto.
    if (strlen($senha) < 8) {
        echo "<script>alert('A senha deve ter pelo menos 8 caracteres.'); window.history.back();</script>";
        exit;
    }

    if (!hash_equals($senha, $confirmar_senha)) {
        echo "<script>alert('Erro na confirmação de senha'); window.history.back();</script>";
        exit;
    }

    try {
        // Verifica se o e-mail já existe (prepared statement, seguro contra SQL Injection)
        $sql = "SELECT id, senha, provedor FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Conta existe. Verifica se veio do Google e ainda não tem senha definida.
            if ($usuario['provedor'] === 'google' && empty($usuario['senha'])) {

                // Em vez de bloquear, aproveita e define a senha nessa conta já existente
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                $sqlUpdate = "UPDATE usuarios SET senha = ?, provedor = 'ambos' WHERE email = ?";
                $stmtUpdate = $pdo->prepare($sqlUpdate);

                if ($stmtUpdate->execute([$senhaHash, $email])) {
                    $_SESSION['mensagem'] = "Senha adicionada à sua conta com sucesso! Agora você pode entrar com e-mail e senha.";
                    header("Location: ../front-end/login.php");
                    exit;
                } else {
                    echo "<script>alert('Erro ao definir senha!'); window.history.back();</script>";
                    exit;
                }

            } else {
                // Já existe conta local de verdade (com senha própria) → erro real
                echo "<script>alert('Este e-mail já está cadastrado!'); window.history.back();</script>";
                exit;
            }
        }

        // E-mail não existe ainda → cria conta nova normalmente
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sqlInsert = "INSERT INTO usuarios (nome, email, senha, telefone, provedor) 
                      VALUES (?, ?, ?, ?, 'local')";
        $stmtInsert = $pdo->prepare($sqlInsert);

        if ($stmtInsert->execute([$nome, $email, $senhaHash, $telefone])) {
            $_SESSION['mensagem'] = "Cadastro realizado com sucesso!";
            header("Location: ../front-end/login.php");
            exit;
        } else {
            echo "<script>alert('Erro ao cadastrar!'); window.history.back();</script>";
            exit;
        }
    } catch (PDOException $e) {
        // Não expõe detalhes do banco ao usuário (evita vazar estrutura
        // interna); registra o detalhe técnico apenas no log do servidor.
        tratar_erro_bd($e, 'salvar_cadastro');
        echo "<script>alert('Não foi possível concluir o cadastro agora. Tente novamente em instantes.'); window.history.back();</script>";
        exit;
    }
}
