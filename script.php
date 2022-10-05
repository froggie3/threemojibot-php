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
			
			#echo var_dump(CHAR_ARRAY);
		}
		else {
			exit;
		}
	}
	
	public function generate_word_test(): ?string 
	{
		$word = 'みかる';
		echo var_dump($word);
		echo var_dump(self::CHAR_DEF);

		if (
			(preg_match(self::CHAR_DEF['SCREEN'][0], $word))
		   	or 
			(preg_match(self::CHAR_DEF['SCREEN'][1], $word))
		)
		{
			return 'bad';
		}
		else
		{
			return 'ok'; 
		}
	}

	// ====================================================================
	//        IF YOU ARE NOW EDITING THIS FOR TEST NEVER TOUCH THIS
	// ====================================================================
	
	public function generate_word( array $a, int $b,): ?string 
	{
		$i = 0; 
		#echo var_dump($i);
		
		while($i <= 3)
		{
			$out_buffer_chunk[] = $a[rand(0, $b - 1)];	// 配列の中から1つえらんでバッファに格納
			if(count($out_buffer_chunk)>= 3)	 // 配列のキーの総数が一定以上に達したら文字列に結合
			{
				$words_merged = implode($out_buffer_chunk);		
				
				if (
					(preg_match(self::CHAR_DEF['SCREEN'][0], $words_merged))
					or 
					(preg_match(self::CHAR_DEF['SCREEN'][1], $words_merged))
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
				#echo var_dump($i);
			}
		}
		$out_buffer_chunk = [];	 // バッファを破棄
	}

	public function post(): ?string
	{
		#echo var_dump ( $instance-> generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT) );
		
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

// these are flags; uncomment these as you need

// if you would like to post generated words
$instance->post();

// if you would like to test if the regex are valid 
#echo $instance->generate_word_test();

// if you would like to output the generated word into stdout 
#echo $instance->generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT);
