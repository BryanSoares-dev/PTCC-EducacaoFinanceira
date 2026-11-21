<?php
declare(strict_types=1);

final class SunsetUploadException extends RuntimeException {}

/** Retorna um arquivo enviado ou uma mensagem de validação específica. */
function api_file_field(string $field): array
{
    $file = $_FILES[$field] ?? null;
    if (!is_array($file)) {
        throw new SunsetUploadException('Selecione o arquivo obrigatório para continuar.');
    }

    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'O arquivo excede o limite configurado pelo PHP. Use um arquivo de até 100 MB ou ajuste upload_max_filesize no php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o limite permitido pelo formulário.',
            UPLOAD_ERR_PARTIAL => 'O upload foi interrompido. Tente enviar o arquivo novamente.',
            UPLOAD_ERR_NO_FILE => 'Selecione um arquivo para continuar.',
            UPLOAD_ERR_NO_TMP_DIR => 'A pasta temporária de upload do PHP não está disponível.',
            UPLOAD_ERR_CANT_WRITE => 'O servidor não conseguiu gravar o arquivo enviado.',
            UPLOAD_ERR_EXTENSION => 'Uma extensão do PHP bloqueou o upload do arquivo.',
        ];
        throw new SunsetUploadException($messages[$error] ?? 'Falha ao receber o arquivo enviado.');
    }
    if (empty($file['tmp_name']) || !is_uploaded_file((string)$file['tmp_name'])) {
        throw new SunsetUploadException('O arquivo enviado não pôde ser validado pelo servidor.');
    }
    return $file;
}

function api_cloudinary_config(): array
{
    static $config;
    if ($config !== null) return $config;
    $values = [];
    $file = 'C:\\xampp\\cloudinary.env';
    if (is_file($file) && is_readable($file)) {
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
            $values[trim($key)] = trim($value, " \t\"'");
        }
    }
    foreach (['CLOUDINARY_CLOUD_NAME', 'CLOUDINARY_API_KEY', 'CLOUDINARY_API_SECRET'] as $key) {
        $values[$key] = (string)(getenv($key) ?: ($values[$key] ?? ''));
    }
    return $config = $values;
}

function api_cloudinary_enabled(): bool
{
    $c = api_cloudinary_config();
    return $c['CLOUDINARY_CLOUD_NAME'] !== '' && $c['CLOUDINARY_API_KEY'] !== '' && $c['CLOUDINARY_API_SECRET'] !== '' && function_exists('curl_init');
}

function api_cloudinary_upload(string $tmp, string $mime, string $folder, string $kind): string
{
    set_time_limit(0);
    $c = api_cloudinary_config();
    $timestamp = time();
    $remoteFolder = 'ptcc_educacao_financeira/' . $folder;
    $signature = sha1('folder=' . $remoteFolder . '&timestamp=' . $timestamp . $c['CLOUDINARY_API_SECRET']);
    $endpoint = 'https://api.cloudinary.com/v1_1/' . rawurlencode($c['CLOUDINARY_CLOUD_NAME']) . '/' . (in_array($kind, ['video','audio'], true) ? 'video' : 'image') . '/upload';
    $post = ['file'=>new CURLFile($tmp,$mime,basename($tmp)),'api_key'=>$c['CLOUDINARY_API_KEY'],'timestamp'=>(string)$timestamp,'folder'=>$remoteFolder,'signature'=>$signature];
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$post,CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>900,CURLOPT_CONNECTTIMEOUT=>20,CURLOPT_LOW_SPEED_LIMIT=>1024,CURLOPT_LOW_SPEED_TIME=>45]);
    $raw = curl_exec($ch); $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); $error = curl_error($ch); curl_close($ch);
    $data = is_string($raw) ? json_decode($raw, true) : null;
    if ($status < 200 || $status >= 300 || !is_array($data) || empty($data['secure_url'])) {
        $detail = is_array($data) ? (string)($data['error']['message'] ?? '') : '';
        throw new SunsetUploadException('O Cloudinary recusou o upload.' . ($detail !== '' ? ' ' . $detail : ($error !== '' ? ' ' . $error : '')));
    }
    return (string)$data['secure_url'];
}

/** Armazena a mídia no Cloudinary quando configurado; caso contrário usa o disco local. */
function api_store_upload(array $file, string $folder, string $kind): string
{
    $tmp = (string)($file['tmp_name'] ?? ''); $size = (int)($file['size'] ?? 0);
    if ($tmp === '' || !is_uploaded_file($tmp) || $size <= 0) throw new SunsetUploadException('O arquivo enviado é inválido ou está vazio.');
    $isVideo = $kind === 'video'; $isAudio = $kind === 'audio'; $maxBytes = $isVideo ? 100 * 1024 * 1024 : ($isAudio ? 20 * 1024 * 1024 : 8 * 1024 * 1024);
    if ($size > $maxBytes) throw new SunsetUploadException($isVideo ? 'Vídeos maiores que 100 MB não são aceitos.' : ($isAudio ? 'Áudios maiores que 20 MB não são aceitos.' : 'Imagens maiores que 8 MB não são aceitas.'));
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $extensions = $isVideo ? ['video/mp4'=>'mp4','application/mp4'=>'mp4'] : ($isAudio ? ['audio/mpeg'=>'mp3','audio/mp3'=>'mp3','audio/mp4'=>'m4a','audio/x-m4a'=>'m4a','audio/wav'=>'wav','audio/x-wav'=>'wav','audio/ogg'=>'ogg','application/ogg'=>'ogg','audio/webm'=>'webm','video/webm'=>'webm'] : ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp']);
    if (!isset($extensions[$mime])) throw new SunsetUploadException($isVideo ? 'Envie um vídeo no formato MP4.' : ($isAudio ? 'Envie um áudio MP3, M4A, WAV, OGG ou WebM.' : 'Envie uma imagem JPG, PNG ou WebP.'));
    if (!in_array($folder, ['videos','thumbs','social','stories','groups','chat'], true)) throw new SunsetUploadException('Destino de upload inválido.');
    if (api_cloudinary_enabled()) return api_cloudinary_upload($tmp, $mime, $folder, $kind);
    $destinationDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder;
    if (!is_dir($destinationDir) && !mkdir($destinationDir, 0775, true) && !is_dir($destinationDir)) throw new SunsetUploadException('O servidor não conseguiu criar a pasta de uploads.');
    if (!is_writable($destinationDir)) throw new SunsetUploadException('A pasta de uploads não tem permissão de escrita.');
    try { $name = bin2hex(random_bytes(18)) . '.' . $extensions[$mime]; } catch (Throwable $e) { throw new SunsetUploadException('Não foi possível gerar o nome seguro do arquivo.'); }
    $relative = 'uploads/' . $folder . '/' . $name;
    if (!move_uploaded_file($tmp, $destinationDir . DIRECTORY_SEPARATOR . $name)) throw new SunsetUploadException('O servidor não conseguiu salvar o arquivo. Verifique a permissão da pasta community/uploads.');
    return $relative;
}

function api_delete_stored(?string $relative): void
{
    if (!$relative || preg_match('~^https?://~i', $relative)) return;
    if (!preg_match('~^uploads/(videos|thumbs|social|stories|groups|chat)/[a-f0-9]{36}\.(mp4|mp3|m4a|wav|ogg|webm|jpg|png|webp)$~D', $relative)) return;
    $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($file)) @unlink($file);
}
