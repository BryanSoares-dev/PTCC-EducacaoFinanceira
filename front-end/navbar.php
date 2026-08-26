<link rel="stylesheet" href="../css/navbar.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/style-perfil.css">


<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdmin = false;
if (isset($_SESSION['id'])) {
    require_once __DIR__ . '/../back-end/conexao.php';
    $stmt = $pdo->prepare("SELECT tipo FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $dadosTipo = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($dadosTipo && ($dadosTipo['tipo'] ?? '') === 'admin') {
        $isAdmin = true;
    }
}
?>

<header>

    <a href="../front-end/home.php">
        <img class="logo" src="../img/logo.png" alt="Logo">
    </a>

    <nav>
        <ul>
            <li><a href="home.php">Início</a></li>
            <li><a href="aprendizado.php">Educação</a></li>
            <li><a href="calculadora.php">Calculadora</a></li>
            <li><a href="carteira.php">Carteira</a></li>
            <li><a href="investimentos_Diversificacao.php">Investimentos</a></li>
            <li><a href="analisador.php">Analisador</a></li>
            <?php if ($isAdmin): ?>
                <li><a href="../admin/index.php">Admin</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="home_bot">

        <?php if (isset($_SESSION['id'])): ?>

            <?php
                $usuario = $_SESSION['usuario'] ?? [];

                $nomeCompleto = trim($usuario['nome'] ?? '');

                $partes = $nomeCompleto !== ''
                    ? preg_split('/\s+/', $nomeCompleto)
                    : ['Usuário'];

                $primeiroNome = $partes[0];

                $ultimoNome = count($partes) > 1
                    ? $partes[count($partes) - 1]
                    : null;

                $nomeExibicao = $ultimoNome
                    ? $primeiroNome . " " . $ultimoNome
                    : $primeiroNome;

                $inicial = mb_strtoupper(
                    mb_substr($primeiroNome, 0, 1)
                );

                $temFoto = !empty($usuario['foto']);

                $fotoUsuario = $temFoto
                    ? $usuario['foto']
                    : null;
            ?>

            <div class="user_area">

                <span class="user_name">
                    <strong>
                        <?= htmlspecialchars($nomeExibicao) ?>
                    </strong>
                </span>

                <div class="user_dropdown">

                    <a href="#" id="avatarBtn" aria-label="Abrir menu do usuário">

                        <?php if ($temFoto): ?>

                            <img
                                src="<?= htmlspecialchars($fotoUsuario) ?>"
                                class="avatar_img"
                                alt="Avatar"
                            >

                        <?php else: ?>

                            <div class="avatar_placeholder">
                                <?= htmlspecialchars($inicial) ?>
                            </div>

                        <?php endif; ?>

                    </a>


                    <div class="dropdown_menu" id="dropdownMenu">

                        <div class="dropdown_header">

                            <?php if ($temFoto): ?>

                                <img
                                    src="<?= htmlspecialchars($fotoUsuario) ?>"
                                    alt="Avatar"
                                    class="avatar_img"
                                >

                            <?php else: ?>

                                <div class="avatar_placeholder">
                                    <?= htmlspecialchars($inicial) ?>
                                </div>

                            <?php endif; ?>


                            <div>

                                <strong>
                                    <?= htmlspecialchars($nomeExibicao) ?>
                                </strong>

                                <small>
                                    Usuário
                                </small>

                            </div>

                        </div>


                        <a
                            href="perfil.php"
                            class="dropdown_item"
                        >
                            Ver Perfil
                        </a>


                        <a
                            href="configuracoes.php"
                            class="dropdown_item"
                        >
                            Configurações
                        </a>


                        <hr>


                        <a
                            href="../back-end/logout.php"
                            class="dropdown_item logout"
                        >
                            Sair
                        </a>

                    </div>

                </div>

            </div>


        <?php else: ?>


            <a href="cadastro.php">
                <button
                    type="button"
                    class="button_log"
                >
                    Registrar
                </button>
            </a>


            <a href="login.php">
                <button
                    type="button"
                    class="button_log conectar"
                >
                    Entrar
                </button>
            </a>


        <?php endif; ?>

    </div>

</header>


<script>
(function () {

    if (window.__navbarInitialized) return;

    window.__navbarInitialized = true;


    const avatarBtn = document.getElementById("avatarBtn");
    const dropdownMenu = document.getElementById("dropdownMenu");


    if (avatarBtn && dropdownMenu) {

        avatarBtn.addEventListener("click", function (e) {

            e.preventDefault();
            e.stopPropagation();

            dropdownMenu.classList.toggle("active");

        });


        document.addEventListener("click", function (e) {

            if (
                !avatarBtn.contains(e.target) &&
                !dropdownMenu.contains(e.target)
            ) {

                dropdownMenu.classList.remove("active");

            }

        });

    }

})();
</script>
