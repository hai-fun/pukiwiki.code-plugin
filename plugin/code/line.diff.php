<?php
/**
 * diff �����������ե�����
 * �Իظ��⡼����
 */

$switchHash['!'] = PLUGIN_CODE_IDENTIFIRE_CHAR;   // changed
$switchHash['|'] = PLUGIN_CODE_IDENTIFIRE_CHAR;   // changed
$switchHash['+'] = PLUGIN_CODE_IDENTIFIRE_WORD;   // added
$switchHash['>'] = PLUGIN_CODE_IDENTIFIRE_CHAR;   // added
$switchHash[')'] = PLUGIN_CODE_IDENTIFIRE_CHAR;   // added
$switchHash['-'] = PLUGIN_CODE_IDENTIFIRE_WORD;   // removed
$switchHash['<'] = PLUGIN_CODE_IDENTIFIRE_CHAR;   // removed
$switchHash['('] = PLUGIN_CODE_IDENTIFIRE_CHAR;   // removed
$switchHash['*'] = PLUGIN_CODE_IDENTIFIRE_CHAR;   // control
$switchHash['\\']= PLUGIN_CODE_IDENTIFIRE_CHAR;   // control
$switchHash['@'] = PLUGIN_CODE_IDENTIFIRE_CHAR;   // control

$mkoutline = $option['outline'] = 0; // �����ȥ饤��⡼���Բ� 
$linemode = 1; // �������Ϥ��ʤ�

// 
$code_identifire = array(
	 '-' => Array(
		  '---',
		 ),
	 '+' => Array(
		  '+++',
		 ),
	 );


// ���������
$switchHash['#'] = PLUGIN_CODE_COMMENT;	// �����Ȥ� # ������Ԥޤ�

$code_css = Array(
					   'changed', //
					   'added',   //
					   'removed', //

					   'system', //
);

$code_keyword = Array(
						   '!' => 1,
						   '|' => 1,

						   '+' => 2,
						   '>' => 2,
						   ')' => 2,
						   '/' => 2,

						   '-' => 3,
						   '<' => 3,
						   '(' => 3,
						   '\\' => 3,

						   '*' => 4,
						   '\\' => 4,
						   '@' => 4,
						   '---' => 4,
						   '+++' => 4,
);
?>