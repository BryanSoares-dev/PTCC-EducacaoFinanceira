<?php
declare(strict_types=1);

function api_extra_optional_file(string $field, string $kind): ?string
{
    $file = $_FILES[$field] ?? null;
    if (!is_array($file) || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    return api_store_upload(api_file_field($field), 'chat', $kind);
}

function api_extra_post(PDO $pdo, int $uid, string $action, array $json): bool
{
    if ($action === 'send_message') {
        $data = $json + $_POST;
        $other = filter_var($data['user_id'] ?? null, FILTER_VALIDATE_INT);
        $body = sunset_conteudo_exigir((string)($data['body'] ?? ''), 'mensagem privada');
        $field = isset($_FILES['audio']) ? 'audio' : (isset($_FILES['attachment']) ? 'attachment' : 'image');
        $file = $_FILES[$field] ?? null;
        $hasFile = is_array($file) && (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
        if (!$other || (!$body && !$hasFile) || api_length($body) > 4000) api_fail('Escreva uma mensagem ou anexe uma imagem ou áudio.');
        api_require_friend($pdo, $uid, (int)$other);
        $attachment = null;
        try {
            if ($hasFile) {
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);
                $kind = (str_starts_with((string)$mime, 'audio/') || in_array($mime, ['video/webm','application/ogg'], true)) ? 'audio' : 'image';
                $attachment = api_store_upload(api_file_field($field), 'chat', $kind);
            }
            $s = $pdo->prepare('INSERT INTO comunidade_mensagens_privadas(remetente_id,destinatario_id,corpo,anexo_path) VALUES(?,?,?,?)');
            $s->execute([$uid, (int)$other, $body, $attachment]);
        } catch (Throwable $e) { api_delete_stored($attachment); throw $e; }
        comunidade_json(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
    }

    if ($action === 'group_message') {
        $data = $json + $_POST;
        $gid = filter_var($data['group_id'] ?? null, FILTER_VALIDATE_INT);
        $tab = (string)($data['tab'] ?? 'chat');
        $body = sunset_conteudo_exigir((string)($data['body'] ?? ''), 'mensagem do grupo');
        $field = isset($_FILES['audio']) ? 'audio' : (isset($_FILES['attachment']) ? 'attachment' : 'image');
        $file = $_FILES[$field] ?? null;
        $hasFile = is_array($file) && (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
        if (!$gid || (!$body && !$hasFile) || api_length($body) > 1500) api_fail('Escreva uma mensagem ou anexe uma imagem ou áudio.');
        $group = api_group_admin($pdo, (int)$gid, $uid); $aba = null; $canal = 'chat';
        if ($tab === 'announcements') $canal = 'announcements';
        elseif (preg_match('/^(?:custom-)(\d+)$/', $tab, $m)) {
            $aba = (int)$m[1]; $s = $pdo->prepare('SELECT tipo FROM comunidade_abas_grupo WHERE id=? AND grupo_id=?'); $s->execute([$aba, $gid]); $kind = $s->fetchColumn();
            if (!$kind || $kind === 'call') api_fail('Essa aba não aceita mensagens.', 400); $canal = $kind === 'announcements' ? 'announcements' : 'chat';
        } elseif ($tab !== 'chat') api_fail('Aba inválida.');
        if ($canal === 'announcements' && !$group['is_admin']) api_fail('Apenas o administrador pode publicar avisos.', 403);
        $attachment = null;
        try {
            if ($hasFile) {
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);
                $kind = (str_starts_with((string)$mime, 'audio/') || in_array($mime, ['video/webm','application/ogg'], true)) ? 'audio' : 'image';
                $attachment = api_store_upload(api_file_field($field), 'chat', $kind);
            }
            $s = $pdo->prepare('INSERT INTO comunidade_mensagens_grupo(grupo_id,aba_id,canal,usuario_id,corpo,anexo_path) VALUES(?,?,?,?,?,?)');
            $s->execute([$gid, $aba, $canal, $uid, $body, $attachment]);
        } catch (Throwable $e) { api_delete_stored($attachment); throw $e; }
        comunidade_json(['ok'=>true]);
    }

    if ($action === 'delete_private_message') {
        $id = filter_var($json['message_id'] ?? null, FILTER_VALIDATE_INT); if (!$id) api_fail('Mensagem inválida.');
        $s = $pdo->prepare('SELECT anexo_path FROM comunidade_mensagens_privadas WHERE id=? AND remetente_id=?'); $s->execute([$id, $uid]); $path = $s->fetchColumn();
        if ($path === false) api_fail('Só é possível apagar as próprias mensagens.', 403);
        $s = $pdo->prepare('DELETE FROM comunidade_mensagens_privadas WHERE id=? AND remetente_id=?'); $s->execute([$id, $uid]); api_delete_stored((string)$path); comunidade_json(['ok'=>true]);
    }

    if ($action === 'delete_post') {
        $id = filter_var($json['post_id'] ?? null, FILTER_VALIDATE_INT); if (!$id) api_fail('Publicação inválida.');
        $s = $pdo->prepare('DELETE FROM comunidade_publicacoes WHERE id=? AND usuario_id=?'); $s->execute([$id, $uid]); if (!$s->rowCount()) api_fail('Publicação não encontrada ou não pertence a você.', 403); comunidade_json(['ok'=>true]);
    }

    if ($action === 'delete_video') {
        $id = filter_var($json['video_id'] ?? null, FILTER_VALIDATE_INT); if (!$id) api_fail('Vídeo inválido.');
        $s = $pdo->prepare('SELECT url,miniatura FROM comunidade_videos WHERE id=? AND usuario_id=?'); $s->execute([$id, $uid]); $row = $s->fetch(PDO::FETCH_ASSOC); if (!$row) api_fail('Vídeo não encontrado ou não pertence a você.', 403);
        $s = $pdo->prepare('DELETE FROM comunidade_videos WHERE id=? AND usuario_id=?'); $s->execute([$id, $uid]); api_delete_stored((string)$row['url']); api_delete_stored((string)$row['miniatura']); comunidade_json(['ok'=>true]);
    }

    if ($action === 'delete_visual') {
        $id = filter_var($json['post_id'] ?? null, FILTER_VALIDATE_INT); if (!$id) api_fail('Publicação visual inválida.');
        $s = $pdo->prepare('SELECT caminho FROM comunidade_midias_visuais m JOIN comunidade_posts_visuais p ON p.id=m.post_id WHERE m.post_id=? AND p.usuario_id=?'); $s->execute([$id, $uid]); $paths = $s->fetchAll(PDO::FETCH_COLUMN); if (!$paths) api_fail('Publicação não encontrada ou não pertence a você.', 403);
        $s = $pdo->prepare('DELETE FROM comunidade_posts_visuais WHERE id=? AND usuario_id=?'); $s->execute([$id, $uid]); foreach ($paths as $path) api_delete_stored((string)$path); comunidade_json(['ok'=>true]);
    }

    if ($action === 'delete_visual_comment') {
        $id = filter_var($json['comment_id'] ?? null, FILTER_VALIDATE_INT); if (!$id) api_fail('Comentário inválido.');
        $s = $pdo->prepare('SELECT c.usuario_id,p.usuario_id AS post_owner FROM comunidade_comentarios_visuais c JOIN comunidade_posts_visuais p ON p.id=c.post_id WHERE c.id=?'); $s->execute([$id]); $row = $s->fetch(PDO::FETCH_ASSOC); if (!$row) api_fail('Comentário não encontrado.', 404);
        if ((int)$row['usuario_id'] !== $uid && (int)$row['post_owner'] !== $uid) api_fail('Somente o autor ou dono da publicação pode apagar este comentário.', 403);
        $pdo->prepare('DELETE FROM comunidade_comentarios_visuais WHERE id=?')->execute([$id]); comunidade_json(['ok'=>true]);
    }

    return false;
}
