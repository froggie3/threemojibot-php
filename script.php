<?php
class threemoji
{
    private const CHAR_DEF = [
        'DICT' =>
            'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよわんがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょ',
        'SCREEN' => [
            ['ぁ', 'ぃ', 'ぅ', 'ぇ', 'ぉ', 'っ', 'ゃ', 'ゅ', 'ょ'],
            ['き', 'し', 'ち', 'に', 'ひ', 'み', 'り', 'ぎ', 'じ', 'び', 'ぴ'],
            ['ゃ', 'ゅ', 'ょ'],
            ['を', 'ん'],
        ]
    ];

    public function is_valid($var)
    {
        $words_merged = $var;
        $flag = 0;
        $pos = 0;

        // 小文字が語頭ないし語尾にあるとき
        foreach (self::CHAR_DEF['SCREEN'][0] as $var) {
            $pos = mb_strpos($words_merged, $var);
            
            // (int) $pos のとき
            if ($pos !== false and ( $pos === 0 or $pos === 2)) {
                return false;
            }
        }
        
        // その他特殊な文字が語頭にあるとき
        foreach (self::CHAR_DEF['SCREEN'][3] as $var) {
            $pos = mb_strpos($words_merged, $var);
            
            // (int) $pos のとき
            if ($pos !== false and $pos === 0) {
                return false;
            }
        }

        // 小文字より1文字前の文字を参照して取得
        // その文字がが配列にある文字かどうかを比較するために配列をぶんまわしてそれぞれ参照する
        if (true) {
            foreach (self::CHAR_DEF['SCREEN'][2] as $var2) {
                $pos2 = mb_strpos($words_merged, $var2); 

                // (int) $pos のとき
                if ($pos2 !== false) {
                    // 直前の文字を取得して代入する
                    $char_prev_ref = mb_substr($words_merged, $pos2 - 1, 1);

                    foreach (self::CHAR_DEF['SCREEN'][1] as $var3) {
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
        } else {}
        return true;
    }


    public function __construct()
    {
        if (!empty(self::CHAR_DEF['DICT'])) {
            // splits given characters with string by bytes specified and then contains as array items.
            // note that Japanese characters are splited by 3 bytes in UTF-8.
            $this->CHAR_ARRAY = preg_split(
                '//u',
                self::CHAR_DEF['DICT'],
                -1,
                PREG_SPLIT_NO_EMPTY
            );
            $this->CHAR_COUNT = count($this->CHAR_ARRAY);
            #var_dump(CHAR_ARRAY);
        } else {
            exit();
        }
    }

    // ==============================================================
    //     IF YOU ARE NOW EDITING THIS FOR TEST NEVER TOUCH THIS
    // ==============================================================

    public function generate_word(array $a, int $b): ?string
    {
        $i = 0;
        #var_dump($i);
        do {
            $out_buffer_chunk[] = $a[rand(0, $b - 1)]; // 配列の中から1つえらんでバッファに格納
            if (count($out_buffer_chunk) >= 3) {
                // 配列のキーの総数が一定以上に達したら文字列に結合
                $words_merged = implode($out_buffer_chunk);

                $validity = $this->is_valid( $words_merged );

                if ( $validity === false ) {
                    #echo 'Retrying...';
                    return $this->generate_word($a, $b);
                    #return "";
                } else {
                    $result = $words_merged . PHP_EOL;
                    return $result;
                    break;
                }
            } else {
                $i++;
                #var_dump($i);
            }
        } while ($i <= 3);
        $out_buffer_chunk = []; // バッファを破棄
    }

    public function post(): ?string
    {
        #var_dump ( $instance-> generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT) );
        $message = [
            'content' => $this->generate_word(
                $this->CHAR_ARRAY,
                $this->CHAR_COUNT
            )
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

$instance = new threemoji();
$options = getopt('p::t::w::a::');

#var_dump($options);

// with no option returns an empty array
if (empty($options)) {
    // posts generated words
    $instance->post();
}

// option to output the generated word into stdout
if (isset($options['w'])) {
    echo $instance->generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT);
}

// option to test if the regex are valid
if (isset($options['t'])) {
    $itest_ptn = ['っみき', 'しじみ', 'ししゅ', 'しまゅ',];
    #$test_ptn = ['ししゅ',];
    
    foreach ($test_ptn as $item) {
        echo "$item: ";
        echo ( $instance->is_valid($item) === true ) ? "true" : "false";
        echo "\n";
    }
}

// option to test if the regex are valid
if (isset($options['a'])) {
    var_dump($instance->CHAR_ARRAY);
}
