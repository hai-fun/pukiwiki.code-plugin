<?php
/**
 * C++/CLI �����������ե�����
 */

// C/C++ ������������
require('keyword.c.php');

// ���ڡ�����ޤ७�����
$code_space_keyword += Array(
	'for each' => 2,
	'interface class' => 2,
	'interface struct' => 2,
	'ref class' => 2,
	'ref struct' => 2,
	'value class' => 2,
	'value struct' => 2,
	);

// �̾省������ɲ�
$code_keyword += Array(
	// ʸ̮��¸�������
	'abstract' => 2,
	'delegate' => 2,
	'event' => 2,
	'finally' => 2,
	'generic' => 2,
	'initonly' => 2,
	'internal' => 2,
	'literal' => 2,
	'override' => 2,
	'property' => 2,
	'sealed' => 2,
	'where' => 2,

	// ����¾�Υ������
	'gcnew' => 2,
	'in' => 2,
	'nullptr' => 2,

	// cli::* �������
	'array' => 2,
	'interior_ptr' => 2,
	'pin_ptr' => 2,
	'safe_cast' => 2,

	'#using' => 3,
	);

?>