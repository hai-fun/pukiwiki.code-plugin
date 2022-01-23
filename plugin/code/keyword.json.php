<?php
/**
 * Json
 */
$switchHash[':'] = PLUGIN_CODE_IDENTIFIRE_CHAR;
$switchHash['\''] = PLUGIN_CODE_STRING_LITERAL;


// アウトライン用
if($mkoutline){
  $switchHash['{'] = PLUGIN_CODE_BLOCK_START;
  $switchHash['}'] = PLUGIN_CODE_BLOCK_END;
  $switchHash['['] = PLUGIN_CODE_BLOCK_START;
  $switchHash[']'] = PLUGIN_CODE_BLOCK_END;
}

$code_css = Array(
  'operator',		// オペレータ関数
  'identifier',	// その他の識別子
  'pragma',		// module, import と pragma
  'system',		// 処理系組み込みの奴 __stdcall とか
  );

$code_keyword = Array(
 ':' => 5,
);
?>
