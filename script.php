<?php

declare(strict_types=1);
ini_set('max_execution_time', 1200);

class threemoji
{
    public const CHAR_DEF = "";
    public const CHAR_ARRAY = [];
    public const CHAR_COUNT = 0;

    public function generate_word(array $a, int $b): string
    {
        $words_merged = "";
        $out_buffer_chunk = [];
        $i = 0;

        do {
            // 配列の中から1つえらんでバッファに格納
            $out_buffer_chunk[] = $a[mt_rand(0, $b - 1)];
            $i++;
        } while ($i < 3);

        // 配列のキーの総数が一定以上に達したら文字列に結合
        $words_merged = implode($out_buffer_chunk);

        // バッファを破棄
        $out_buffer_chunk = [];
        return $words_merged;
    }
}

class valid_check
{
    private const SCREEN = [
        ['ぁ', 'ぃ', 'ぅ', 'ぇ', 'ぉ', 'っ', 'ゃ', 'ゅ', 'ょ'],
        ['き', 'し', 'ち', 'に', 'ひ', 'み', 'り', 'ぎ', 'じ', 'び', 'ぴ'],
        ['ゃ', 'ゅ', 'ょ'],
        ['を', 'ん'],
    ];

    public function is_valid(string $gen): bool
    {
        $pos = 0;

        // 小文字が語頭ないし語尾にあるとき
        foreach (self::SCREEN[0] as $var) {
            $pos = mb_strpos($gen, $var);

            // (int) $pos のとき
            if ($pos !== false and ($pos === 0)) {
                $var = "";
                return false;
            }
        }

        // その他特殊な文字が語頭にあるとき
        foreach (self::SCREEN[3] as $var) {
            $pos = mb_strpos($gen, $var);

            // (int) $pos のとき
            if ($pos !== false and $pos === 0) {
                return false;
            }
        }

        // 小文字より1文字前の文字を参照して取得
        // その文字がが配列にある文字かどうかを比較するために配列をぶんまわしてそれぞれ参照する
        foreach (self::SCREEN[2] as $var2) {
            $pos2 = mb_strpos($gen, $var2);

            // (int) $pos のとき
            if ($pos2 !== false) {
                // 直前の文字を取得して代入する
                $char_prev_ref = mb_substr($gen, $pos2 - 1, 1);

                foreach (self::SCREEN[1] as $var3) {
                    // 直前の文字が配列に存在するのは1回だけ
                    if ($char_prev_ref === $var3) {
                        return true;
                    } else {
                        continue;
                    }
                }
                return false;
            } else {
                continue;
            }
            break;
        }
        return true;
    }
}

class cfgperser
{
    public $configFile;
    public function iscfgvalid(): bool
    {
        $bool = (file_exists($this->configFile)) ? true : false;
        return $bool;
    }

    public function format(): ?string
    {
        // opens config file and eliminates unwanted spaces
        if ($this->iscfgvalid() === true) {

            // $configFile = path for config file,
            $f = file($this->configFile, 0);

            // output
            $o = str_replace(PHP_EOL, '', array_pop($f));
            return $o;
        } else {
            // output error message
            $msg = $this->configFile . 'not found';
            error_log($msg);
            exit(1);
            return null;
        }
    }
}

class webhook
{
    public $url = "";
    public $msg = [];
    public function send_to_discord(): void
    {
        // options as a context stream
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($this->msg),
            ],
        ];
        $context = stream_context_create($options);
        $fp = fopen($this->url, 'r', false, $context);

        fpassthru($fp);
        fclose($fp);
    }
}

$options = getopt('pt:wi:n:', ['post', 'test:', 'write', 'iteration:', 'interval:']);
$iteration = (isset($options['i']) === true) ? (int) $options['i'] : 1;
$interval = (isset($options['n']) === true) ? (int) $options['n'] : 1;

function gen_word(): string
{
    $threemoji = new threemoji();
    $check = new valid_check();


    // push
    $threemoji->CHAR_DEF =
        'あいうえお' .
        'かきくけこ' .
        'さしすせそ' .
        'たちつてと' .
        'なにぬねの' .
        'はひふへほ' .
        'まみむめも' .
        'やゆよ' .
        'わん' .
        'がぎぐげご' .
        'ざじずぜぞ' .
        'だぢづでど' .
        'ばびぶべぼ' .
        'ぱぴぷぺぽ' .
        'ぁぃぅぇぉ' .
        'っゃゅょ';

    // splits given characters with string by bytes specified and then contains as array items
    $threemoji->CHAR_ARRAY = preg_split('//u', $threemoji->CHAR_DEF, -1, PREG_SPLIT_NO_EMPTY);
    $threemoji->CHAR_COUNT = count($threemoji->CHAR_ARRAY);


    do {
        // gen a word
        $generated = $threemoji->generate_word($threemoji->CHAR_ARRAY, $threemoji->CHAR_COUNT);

        // check if a gen word is valid
        $validity = $check->is_valid($generated);

        if ($validity === true) {
            break;
        }

        # どんな感じで再生成されているのか見たいときは下のフラグをオフに
        # else { echo $generated . "->"; }
    } while ($validity === false);

    return $generated;
}

// posts generated words
if (empty($options) === true) {
    $generated = gen_word();
    echo <<<EOM
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/jpeg" href="./assets/icon.jpg">
        <title>ひらがな3文字</title>
    </head>
    <body>
    EOM . PHP_EOL;
    echo '    <p>' . $generated .  '</p>' . PHP_EOL;

    echo <<<EOM
    </body>
    </html>
    EOM . PHP_EOL;
}

// posts generated words
if (isset($options['p']) === true) {

    function load_url_from_cfg(): string
    {
        // initialize cfg perser
        $cfgperse = new cfgperser;
        $cfgperse->configFile = __DIR__ . '/config/webhook_url.txt';
        return $cfgperse->format();
    }

    function post_discord(string $msg): void
    {
        // initialize external webhook class
        $webhook = new webhook;
        $webhook->url = load_url_from_cfg();
        $webhook->msg = ['content' => $msg];
        $webhook->send_to_discord();

        #var_dump ( $webhook->url, $webhook->msg );
    }

    for ($i = 0; $i < $iteration; $i++) {
        post_discord(gen_word());
        if ($iteration >= 2) {
            sleep($interval);
        }
    }
}

// check if given string is valid
if (isset($options['t']) === true) {
    $check = new valid_check();
    (string) $string = $options['t'];
    $result = "$string: " . ($check->is_valid($string) === true) ? 'true' : 'false';
    echo $result . PHP_EOL;
}

// option to output the generated word into stdout
if (isset($options['w']) || isset($options['stdout']) === true) {
    for ($i = 0; $i < $iteration; $i++) {
        echo gen_word() . PHP_EOL;
        if ($iteration >= 2) {
            sleep($interval);
        }
    }
}
