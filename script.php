<?php
$word = "あいうえおかきくけこさしすせそたちつてとなにぬねのはひふへほまみむめもやゆよをんゔがぎぐげござじずぜぞだぢづでどばびぶべぼぱぴぷぺぽぁぃぅぇぉっゃゅょゎ";
$characters = str_split($word, 3);	 // split characters by each character
#echo var_dump( $characters ); 
$count = count( $characters ); // number of items 
#echo var_dump( $count );

function generate_word(array $a, int $b, string $c) : mixed
{
	$i = 0; 
	#echo var_dump( $i );
	$bad_re_pattern = "/^(を|ん|ぁ|ぃ|ぅ|ぇ|ぉ|っ|ゃ|ゅ|ょ|ゎ)/";;
	
	while ( $i <= 3 )
	{
		$out_buffer_chunk[] = $a[rand( 0, $b - 1 )];	// 配列の中から1つえらんでバッファに格納
		if ( count( $out_buffer_chunk ) >= 3 )	 // 配列のキーの総数が一定以上に達したら文字列に結合
		{
			$words_marged = implode( $out_buffer_chunk );		
			
			if ( preg_match( $bad_re_pattern, $words_marged ) )
			{
				#echo "Retrying...";
				return generate_word($a, $b, $c);
			}
			else
			{
				return $words_marged . "\n";
				break; 
			}
		}
		else
		{
			$i++;
			#echo var_dump( $i );
		}
	}
	$out_buffer_chunk = [];	 // バッファを破棄
}

echo generate_word($characters, $count, $word);

