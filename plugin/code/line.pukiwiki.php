<?php
/**
 *�����������ե�����
 */

$switchHash['#'] = PLUGIN_CODE_BLOCK_START;  // ʣ���԰����б�
$switchHash['}'] = PLUGIN_CODE_BLOCK_END;
$switchHash['&'] = PLUGIN_CODE_SPECIAL_IDENTIFIRE;  // & ����Ϥޤ�ͽ��줢��
$switchHash['*'] = PLUGIN_CODE_IDENTIFIRE_CHAR;  // ���Ф�
$switchHash[','] = PLUGIN_CODE_IDENTIFIRE_CHAR;  // ɽ
$switchHash['|'] = PLUGIN_CODE_IDENTIFIRE_CHAR;  // ɽ
$switchHash[' '] = PLUGIN_CODE_IDENTIFIRE_WORD;  // �����ѽ���
$switchHash['-'] = PLUGIN_CODE_MULTILINE;        // �վ��
$switchHash['+'] = PLUGIN_CODE_MULTILINE;        // �վ��
$switchHash[':'] = PLUGIN_CODE_MULTILINE;        // �վ��
$switchHash['<'] = PLUGIN_CODE_MULTILINE;        // ����
$switchHash['>'] = PLUGIN_CODE_MULTILINE;        // ����
// ʣ���Ԥν�ü����
$multilineEOL = Array(
'#','*',',','|',' ','-','+',':','>','<','/',"\n");
// ����Τߤι��к�
$code_identifire = array(
	 ' ' => Array(
		  " \n",
		 ),
	 );



$capital = 1;                        // ͽ������ʸ����ʸ������̤��ʤ�

// ���������
$switchHash['/'] = PLUGIN_CODE_HEADW_COMMENT;        //  �����Ȥ� ��Ƭ�� // ������Ԥޤ�
$commentpattern = '//';

// �����ȥ饤��
if($mkoutline){
	// $switchHash['{'] = PLUGIN_CODE_BLOCK_START;
  $switchHash['}'] = PLUGIN_CODE_BLOCK_END;
}


$code_css = Array(
  'operator',		// ���ڥ졼���ؿ�
  'identifier',	// ����¾�μ��̻�
  'pragma',		// module, import �� pragma
  'system',		// �������Ȥ߹��ߤ��� __stdcall �Ȥ�
  'header',       // ���Ф�
  'table',        // ɽ
  'list',         // �վ��
  'pre',          // �����ѽ���
  'quote',        // ����
  );

$code_keyword = Array(
					  /*
'#contents' => 2,
'#related' => 2,
'#amazon' => 2,
'#aname' => 2,
'#article' => 2,
'#attach' => 2,
'#back' => 2,
'#br' => 2,
'#bugtrack' => 2,
'#bugtrack_list' => 2,
'#calendar' => 2,
'#calendar2' => 2,
'#calendar_edit' => 2,
'#calendar_read' => 2,
'#calendar_viewer' => 2,
'#clear' => 2,
'#comment' => 2,
'#counter' => 2,
'#freeze' => 2,
'#hr' => 2,
'#img' => 2,
'#include' => 2,
'#includesubmenu' => 2,
'#insert' => 2,
'#lookup' => 2,
'#ls' => 2,
'#ls2' => 2,
'#memo' => 2,
'#menu' => 2,
'#navi' => 2,
'#newpage' => 2,
'#norelated' => 2,
'#online' => 2,
'#paint' => 2,
'#pcomment' => 2,
'#popular' => 2,
'#random' => 2,
'#recent' => 2,
'#ref' => 2,
'#server' => 2,
'#setlinebreak' => 2,
'#showrss' => 2,
'#topicpath' => 2,
'#tracker' => 2,
'#tracker_list' => 2,
'#version' => 2,
'#versionlist' => 2,
'#vote' => 2,
'#code' => 2,
'#pre' => 2,
'&amazon' => 2,
'&aname' => 2,
'&br' => 2,
'&color' => 2,
'&counter' => 2,
'&new' => 2,
'&online' => 2,
'&ref' => 2,
'&ruby' => 2,
'&size' => 2,
'&topicpath' => 2,
'&tracker' => 2,
'&version' => 2,
					  */

 '*' => 5,     // ���Ф�
 ',' => 6,     // ɽ
 '|' => 6,     // ɽ
 '-' => 7,     // �վ��
 '+' => 7,     // �վ��
 ':' => 7,     // �վ��
 ' ' => 8,     // �����ѽ���
 " \n" => 0,   // �ϥ��饤��̵��
 '<' => 9,     // ����
 '>' => 9,     // ����

  );
?>