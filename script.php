<?php
class threemoji
{
    private const CHAR_DEF = 'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよわんがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょ';
    
    public function __construct()
    {
        if ( empty(self::CHAR_DEF) === false ) {
            // splits given characters with string by bytes specified and then contains as array items.
            $this->CHAR_ARRAY = preg_split(
                '//u',
                self::CHAR_DEF,
                -1,
                PREG_SPLIT_NO_EMPTY
            );
            $this->CHAR_COUNT = count($this->CHAR_ARRAY);
        } else {
            exit();
        }
    }
    
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
            if ($pos !== false and ( $pos === 0 or $pos === 2)) {
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

class post
{
    public function post_discord( string $var ): ?string
    {
        $message = [
            'content' => $var
        ];
        include __DIR__ . '/webhook.php';
        if (file_exists($configFile)) {
            send_to_discord($message, getWebhookURL($configFile));
            return null;
        } else {
            #print($configFile . 'not found');
            #touch('./config/webhook_url.txt');
            return null;
        }
    }
}

$options = getopt('p::t::w::a::');
$post = new post();

function gen_word()
{
    $instance = new threemoji();
    $check = new valid_check();

    do { 
        // gen a word
        $generated = $instance->generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT);
        
        // check if a gen word is valid
        $validity = $check->is_valid( $generated );
        if ( $validity === true ) {
            break;
        } 
    } while ( $validity === false );

    return $generated;  
}

$generated = gen_word();

// posts generated words
if (empty($options)) {
    $post->post_discord( $generated );
}

// option to output the generated word into stdout
if (isset($options['w'])) {
    echo $generated; 
}
