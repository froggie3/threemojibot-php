<?php
class threemoji
{
	define('CHAR_DEF', 'あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよをんゔがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょゎ');
	define('BAD_CHAR_DEF', '/^(を|ん|ぁ|ぃ|ぅ|ぇ|ぉ|っ|ゃ|ゅ|ょ|ゎ)/');

	if(defined('CHAR_DEF'))
	{
		define('CHAR_ARRAY', str_split(CHAR_DEF, 3));	 // split characters by each character, Japanese characters are splited by 3 bytes
		#echo var_dump(CHAR_ARRAY); 
		define('CHAR_COUNT', count(CHAR_ARRAY)); // number of items 
		#echo var_dump(CHAR_COUNT);
		
		echo generate_word(CHAR_ARRAY, CHAR_COUNT);
	}

	function generate_word(array $a, int $b) : mixed
	{
		$i = 0; 
		#echo var_dump($i);
		
		while($i <= 3)
		{
			$out_buffer_chunk[] = $a[rand(0, $b - 1)];	// 配列の中から1つえらんでバッファに格納
			if(count($out_buffer_chunk)>= 3)	 // 配列のキーの総数が一定以上に達したら文字列に結合
			{
				$words_marged = implode($out_buffer_chunk);		
				
				if(preg_match(BAD_CHAR_DEF, $words_marged))
				{
					#echo 'Retrying...';
					return generate_word($a, $b);
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
