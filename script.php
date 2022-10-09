<?php
class threemoji
{
	private const CHAR_DEF = array (
		'DICT' => 'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよわんがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょ',
		'SCREEN' => array (
			'/^[をんぁぃぅぇぉっゃゅょ].*$/u',
			'/^(?!.*[きしちにひみりぎじびぴ][ゃゅょ]).*$/um',
			'/^.[ぁぃぅぇぉゃゅょ]+$/um',
	   	),
	);
	
	public function __construct()
	{
		if (!empty(self::CHAR_DEF['DICT']))
		{
			// splits given characters with string by bytes specified and then contains as array items.
			// note that Japanese characters are splited by 3 bytes in UTF-8.
			$this->CHAR_ARRAY = str_split(self::CHAR_DEF['DICT'], 3);
			$this->CHAR_COUNT = 	count($this->CHAR_ARRAY);
			#var_dump(CHAR_ARRAY);
		}
		else {
			exit;
		}

#		$options = getopt('p::t::w::');
#		var_dump($options);
#		if ( isset($options['t'])) 
#		{
#			$this->test_word = $options['t'];
#		}
#		else
#		{
#			$this->test_word = 'みかる';
#		}
	}
	
	public function generate_word_test(): ?string 
	{
		$word = "みかる";
		#$word = $this->test_word;
		#var_dump($word);
		#var_dump(self::CHAR_DEF);
		if (
			(preg_match(self::CHAR_DEF['SCREEN'][0], $word)) or 
			(preg_match(self::CHAR_DEF['SCREEN'][1], $word)) or 
			(preg_match(self::CHAR_DEF['SCREEN'][2], $word))
		)
		{
			return 'to be regenerated' . PHP_EOL;
		}
		else
		{
			return 'complete!' . PHP_EOL; 
		}
	}

	// ====================================================================
	//        IF YOU ARE NOW EDITING THIS FOR TEST NEVER TOUCH THIS
	// ====================================================================
	
	public function generate_word( array $a, int $b,): ?string 
	{
		$i = 0; 
		#var_dump($i);
		do
		{
			$out_buffer_chunk[] = $a[rand(0, $b - 1)];	// 配列の中から1つえらんでバッファに格納
			if(count($out_buffer_chunk)>= 3)	 // 配列のキーの総数が一定以上に達したら文字列に結合
			{
				$words_merged = implode($out_buffer_chunk);		
				
				if (
					(preg_match(self::CHAR_DEF['SCREEN'][0], $words_merged))
					#or 
					#(preg_match(self::CHAR_DEF['SCREEN'][1], $words_merged)) 
					or 
					(preg_match(self::CHAR_DEF['SCREEN'][2], $words_merged))
				)
				{
					#echo 'Retrying...';
					return $this->generate_word($a, $b);
					#return "";
				}
				else
				{
					$result = $words_merged . PHP_EOL;
					return $result; 
					break; 
				}
			}
			else
			{
				$i++;
				#var_dump($i);
			}
		}
		while($i <= 3);
		$out_buffer_chunk = [];	 // バッファを破棄
	}

	public function post(): ?string
	{
		#var_dump ( $instance-> generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT) );
		$message = array( 
			'content' => $this->generate_word($this->CHAR_ARRAY, $this->CHAR_COUNT),
		);
		include __DIR__ . '/webhook.php';
		if (file_exists($configFile))
		{
			send_to_discord($message, getWebhookURL($configFile));
			return null;
		}
		else
		{
			#print($configFile . 'not found');
			#touch('./config/webhook_url.txt');
			return null;
		}	
	}	
}

$instance = new threemoji; 
$options = getopt('p::t::w::');

#var_dump($options);

// with no option returns an empty array
if ( empty($options) )
{
	// posts generated words
	$instance->post();
}

// option to output the generated word into stdout 
if ( isset($options['w']) ) 
{
	echo $instance->generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT);
}

// option to test if the regex are valid 
if ( isset($options['t']) ) 
{
    echo $instance->generate_word_test();
}
