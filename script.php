<?php

declare(strict_types=1);
ini_set('max_execution_time', 1200);
require_once __DIR__ . '/webhook.php';

class threemoji
{
    public const CHAR_DEF = "";
    public const CHAR_ARRAY = [];
    public const CHAR_COUNT = 0;

    public function generate_word(array $a, int $b): ?string
    {
        $words_merged = "";
        $out_buffer_chunk = [];
        $i = 0;

        do {
            $out_buffer_chunk[] = $a[rand(0, $b - 1)]; // 配列の中から1つえらんでバッファに格納
            $i++;
        } while ($i < 3);

        // 配列のキーの総数が一定以上に達したら文字列に結合
        $words_merged = implode($out_buffer_chunk);

        return $words_merged;
        $out_buffer_chunk = []; // バッファを破棄
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
            if ($pos !== false and ($pos === 0 or $pos === 2)) {
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

$options = getopt('p::t::w::a::');


function gen_word()
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
        # else { echo $generated . "→"; }
    } while ($validity === false);

    return $generated;
}


// posts generated words
if (empty($options)) {

    function load_url_from_cfg(): string
    {
        // initialize cfg perser
        $cfgperse = new cfgperser;
        $cfgperse->configFile = __DIR__ . '/config/webhook_url.txt';
        return $cfgperse->format();
    }

    function post_discord($msg): int
    {
        // initialize external webhook class
        $webhook = new webhook;
        $webhook->url = load_url_from_cfg();
        $webhook->msg = ['content' => $msg];
        $webhook->send_to_discord();

        #var_dump ( $webhook->url, $webhook->msg  );
        return 0;
    }

    for ($i = 0; $i < 2; $i++) {
        $generated = gen_word();
        post_discord($generated);
        sleep(600);
    }
}

// option to output the generated word into stdout
if (isset($options['w'])) {
    for ($i = 0; $i < 2; $i++) {
        $generated = gen_word();
        echo $generated . PHP_EOL;
        sleep(1);
    }
}
