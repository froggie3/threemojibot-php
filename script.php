<?php
class threemoji
{
	private const CHAR_DEF = 'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよをんゔがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょゎ';
	private const BAD_CHAR_DEF = '/^(を|ん|ぁ|ぃ|ぅ|ぇ|ぉ|っ|ゃ|ゅ|ょ|ゎ)/';
	
	public function __construct()
	{
		if (!empty(self::CHAR_DEF))
		{
			// splits given characters with string by bytes specified and then contains as array items.
			// note that Japanese characters are splited by 3 bytes in UTF-8.
			$this->CHAR_ARRAY = str_split(self::CHAR_DEF, 3);
			$this->CHAR_COUNT = 	count($this->CHAR_ARRAY);
			
			#echo var_dump(CHAR_ARRAY);
		}
		else {
			exit;
		}
	}
	
	public function generate_word(array $a, int $b) : string 
	{
		$i = 0; 
		#echo var_dump($i);
		
		while($i <= 3)
		{
			$out_buffer_chunk[] = $a[rand(0, $b - 1)];	// 配列の中から1つえらんでバッファに格納
			if(count($out_buffer_chunk)>= 3)	 // 配列のキーの総数が一定以上に達したら文字列に結合
			{
				$words_marged = implode($out_buffer_chunk);		
				
				if(preg_match(self::BAD_CHAR_DEF, $words_marged))
				{
					#echo 'Retrying...';
					return $this->generate_word($a, $b);
					#return "";
				}
				else
				{
					return $words_marged . PHP_EOL;
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
}

$instance = new threemoji;
#echo var_dump ( $instance-> generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT) );
echo $instance-> generate_word($instance->CHAR_ARRAY, $instance->CHAR_COUNT);

