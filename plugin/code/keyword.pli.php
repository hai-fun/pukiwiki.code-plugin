<?php
/**
 * PL/I キーワード定義ファイル
 */

$switchHash['#'] = PLUGIN_CODE_SPECIAL_IDENTIFIRE;  // # から始まる予約語あり

// コメント定義
$switchHash['/'] = PLUGIN_CODE_COMMENT;        //  コメントは /* から */ まで
$code_comment = Array(
	'/' => Array(
				 Array('/^\/\*/', '*/', 2),
	)
);

// アウトライン用
if($mkoutline){
  $switchHash['{'] = PLUGIN_CODE_BLOCK_START;
  $switchHash['}'] = PLUGIN_CODE_BLOCK_END;
}
$outline_def = Array(
					 'do' => Array('end', 1),
					 );
			  
$code_css = Array(
  'operator',		// オペレータ関数
  'identifier',	// その他の識別子
  'pragma',		// module, import と pragma
  'system',		// 処理系組み込みの奴 __stdcall とか
  );

$code_keyword = Array(
	'A'=> 2,
	'ABS'=> 2,
	'ACOS'=> 2,
	'%ACTIVATE'=> 2,
	'ACTUALCOUNT'=> 2,
	'ADD'=> 2,
	'ADDR'=> 2,
	'ADDREL'=> 2,
	'ALIGNED'=> 2,
	'ALLOCATE'=> 2,
	'ALLOC'=> 2,
	'ALLOCATION'=> 2,
	'ALLOCN'=> 2,
	'ANY'=> 2,
	'ANYCONDITION'=> 2,
	'APPEND'=> 2,
	'AREA'=> 2,
	'ASIN'=> 2,
	'ATAN'=> 2,
	'ATAND'=> 2,
	'ATANH'=> 2,
	'AUTOMATIC'=> 2,
	'AUTO'=> 2,
	'B'=> 2,
	'B1'=> 2,
	'B2'=> 2,
	'B3'=> 2,
	'B4'=> 2,
	'BACKUP_DATE'=> 2,
	'BASED'=> 2,
	'BATCH'=> 2,
	'BEGIN'=> 2,
	'BINARY'=> 2,
	'BIN'=> 2,
	'BIT'=> 2,
	'BLOCK_BOUNDARY_FORMAT'=> 2,
	'BLOCK_IO'=> 2,
	'BLOCK_SIZE'=> 2,
	'BOOL'=> 2,
	'BUCKET_SIZE'=> 2,
	'BUILTIN'=> 2,
	'BY'=> 2,
	'BYTE'=> 2,
	'BYTESIZE'=> 2,
	'CALL'=> 2,
	'CANCEL_CONTROL_O'=> 2,
	'CARRIAGE_RETURN_FORMAT'=> 2,
	'CEIL'=> 2,
	'CHAR'=> 2,
	'CHARACTER'=> 2,
	'CLOSE'=> 2,
	'COLLATE'=> 2,
	'COLUMN'=> 2,
	'CONDITION'=> 2,
	'CONTIGUOUS'=> 2,
	'CONTIGUOUS_BEST_TRY'=> 2,
	'CONTROLLED'=> 2,
	'CONVERSION'=> 2,
	'COPY'=> 2,
	'COS'=> 2,
	'COSD'=> 2,
	'COSH'=> 2,
	'CREATION_DATE'=> 2,
	'CURRENT_POSITION'=> 2,
	'DATE'=> 2,
	'DATETIME'=> 2,
	'%DEACTIVATE'=> 2,
	'DECIMAL'=> 2,
	'DEC'=> 2,
	'%DECLARE'=> 2,
	'%DCL'=> 2,
	'DECLARE'=> 2,
	'DCL'=> 2,
	'DECODE'=> 2,
	'DEFAULT_FILE_NAME'=> 2,
	'DEFERRED_WRITE'=> 2,
	'DEFINED'=> 2,
	'DEF'=> 2,
	'DELETE'=> 2,
	'DESCRIPTOR'=> 2,
	'%DICTIONARY'=> 2,
	'DIMENSION'=> 2,
	'DIM'=> 2,
	'DIRECT'=> 2,
	'DISPLAY'=> 2,
	'DIVIDE'=> 2,
	'%DO'=> 2,
	'DO'=> 2,
	'E'=> 2,
	'EDIT'=> 2,
	'%ELSE'=> 2,
	'ELSE'=> 2,
	'EMPTY'=> 2,
	'ENCODE'=> 2,
	'%END'=> 2,
	'END'=> 2,
	'ENDFILE'=> 2,
	'ENDPAGE'=> 2,
	'ENTRY'=> 2,
	'ENVIRONMENT'=> 2,
	'ENV'=> 2,
	'%ERROR'=> 2,
	'ERROR'=> 2,
	'EVERY'=> 2,
	'EXP'=> 2,
	'EXPIRATION_DATE'=> 2,
	'EXTEND'=> 2,
	'EXTENSION_SIZE'=> 2,
	'EXTERNAL'=> 2,
	'EXT'=> 2,
	'F'=> 2,
	'FAST_DELETE'=> 2,
	'%FATAL'=> 2,
	'FILE'=> 2,
	'FILE_ID'=> 2,
	'FILE_ID_TO'=> 2,
	'FILE_SIZE'=> 2,
	'FINISH'=> 2,
	'FIXED'=> 2,
	'FIXEDOVERFLOW'=> 2,
	'FOFL'=> 2,
	'FIXED_CONTROL_FROM'=> 2,
	'FIXED_CONTROL_SIZE'=> 2,
	'FIXED_CONTROL_SIZE_TO'=> 2,
	'FIXED_CONTROL_TO'=> 2,
	'FIXED_LENGTH_RECORDS'=> 2,
	'FLOAT'=> 2,
	'FLOOR'=> 2,
	'FLUSH'=> 2,
	'FORMAT'=> 2,
	'FREE'=> 2,
	'FROM'=> 2,
	'GET'=> 2,
	'GLOBALDEF'=> 2,
	'GLOBALREF'=> 2,
	'%GOTO'=> 2,
	'GOTO'=> 2,
	'GO'=> 2,
	'TO'=> 2,
	'GROUP_PROTETION'=> 2,
	'HBOUND'=> 2,
	'HIGH'=> 2,
	'INDENT'=> 2,
	'%IF'=> 2,
	'IF'=> 2,
	'IGNORE_LINE_MARKS'=> 2,
	'IN'=> 2,
	'%INCLUDE'=> 2,
	'INDEX'=> 2,
	'INDEXED'=> 2,
	'INDEX_NUMBER'=> 2,
	'%INFORM'=> 2,
	'INFORM'=> 2,
	'INITIAL'=> 2,
	'INIT'=> 2,
	'INITIAL_FILL'=> 2,
	'INPUT'=> 2,
	'INT'=> 2,
	'INTERNAL'=> 2,
	'INTO'=> 2,
	'KEY'=> 2,
	'KEYED'=> 2,
	'KEYFROM'=> 2,
	'KEYTO'=> 2,
	'LABEL'=> 2,
	'LBOUND'=> 2,
	'LEAVE'=> 2,
	'LENGTH'=> 2,
	'LIKE'=> 2,
	'LINE'=> 2,
	'LINENO'=> 2,
	'LINESIZE'=> 2,
	'%LIST'=> 2,
	'LIST'=> 2,
	'LOCK_ON_READ'=> 2,
	'LOCK_ON_WRITE'=> 2,
	'LOG'=> 2,
	'LOG10'=> 2,
	'LOG2'=> 2,
	'LOW'=> 2,
	'LTRIM'=> 2,
	'MAIN'=> 2,
	'MANUAL_UNLOCKING'=> 2,
	'MATCH_GREATER'=> 2,
	'MATCH_GREATER_EQUAL'=> 2,
	'MATCH_NEXT'=> 2,
	'MATCH_NEXT_EQUAL'=> 2,
	'MAX'=> 2,
	'MAXIMUM_RECORD_NUMBER'=> 2,
	'MAXIMUM_RECORD_SIZE'=> 2,
	'MAXLENGTH'=> 2,
	'MEMBER'=> 2,
	'MIN'=> 2,
	'MOD'=> 2,
	'MULTIBLOCK_COUNT'=> 2,
	'MULTIBUFFER_COUNT'=> 2,
	'MULTIPLY'=> 2,
	'NEXT_VOLUME'=> 2,
	'%NOLIST'=> 2,
	'NOLOCK'=> 2,
	'NONEXISTENT_RECORD'=> 2,
	'NONRECURSIVE'=> 2,
	'NONVARYING'=> 2,
	'NONVAR'=> 2,
	'NORESCAN'=> 2,
	'NO_ECHO'=> 2,
	'NO_FILTER'=> 2,
	'NO_SHARE'=> 2,
	'NULL'=> 2,
	'OFFSET'=> 2,
	'ON'=> 2,
	'ONARGSLIST'=> 2,
	'ONCHAR'=> 2,
	'ONCODE'=> 2,
	'ONFILE'=> 2,
	'ONKEY'=> 2,
	'ONSOURCE'=> 2,
	'OPEN'=> 2,
	'OPTIONAL'=> 2,
	'OPTIONS'=> 2,
	'OTHERWISE'=> 2,
	'OTHER'=> 2,
	'OUTPUT'=> 2,
	'OVERFLOW'=> 2,
	'OFL'=> 2,
	'OWNER_GROUP'=> 2,
	'OWNER_ID'=> 2,
	'OWNER_MEMBER'=> 2,
	'OWNER_PROTECTION'=> 2,
	'P'=> 2,
	'%PAGE'=> 2,
	'PAGE'=> 2,
	'PAGENO'=> 2,
	'PAGESIZE'=> 2,
	'PARAMETER'=> 2,
	'PARM'=> 2,
	'PICTURE'=> 2,
	'PIC'=> 2,
	'POINTER'=> 2,
	'PTR'=> 2,
	'POSINT'=> 2,
	'POSITION'=> 2,
	'POS'=> 2,
	'PRECISION'=> 2,
	'PREC'=> 2,
	'PRESENT'=> 2,
	'PRINT'=> 2,
	'PRINTER_FORMAT'=> 2,
	'%PROCEDURE'=> 2,
	'%PROC'=> 2,
	'PROCEDURE'=> 2,
	'PROC'=> 2,
	'PROD'=> 2,
	'PROMPT'=> 2,
	'PURGE_TYPE_AHEAD'=> 2,
	'PUT'=> 2,
	'R'=> 2,
	'RANK'=> 2,
	'READ'=> 2,
	'READONLY'=> 2,
	'READ_AHEAD'=> 2,
	'READ_CHECK'=> 2,
	'READ_REGARDLESS'=> 2,
	'RECORD'=> 2,
	'RECORD_ID'=> 2,
	'RECORD_ID_ACCESS'=> 2,
	'RECORD_ID_TO'=> 2,
	'RECURSIVE'=> 2,
	'REFER'=> 2,
	'REFERENCE'=> 2,
	'RELEASE'=> 2,
	'REPEAT'=> 2,
	'%REPLACE'=> 2,
	'RESCAN'=> 2,
	'RESIGNAL'=> 2,
	'RETRIEVAL_POINTERS'=> 2,
	'%RETURN'=> 2,
	'RETURN'=> 2,
	'RETURNS'=> 2,
	'REVERSE'=> 2,
	'REVERT'=> 2,
	'REVISION_DATE'=> 2,
	'REWIND'=> 2,
	'REWIND_ON_CLOSE'=> 2,
	'REWIND_ON_OPEN'=> 2,
	'REWRITE'=> 2,
	'ROUND'=> 2,
	'RTRIM'=> 2,
	'%SBTTL'=> 2,
	'SCALARVARYING'=> 2,
	'SEARCH'=> 2,
	'SELECT'=> 2,
	'SEQUENTIAL'=> 2,
	'SEQL'=> 2,
	'SET'=> 2,
	'SHARED_READ'=> 2,
	'SHARED_WRITE'=> 2,
	'SIGN'=> 2,
	'SIGNAL'=> 2,
	'SIN'=> 2,
	'SIND'=> 2,
	'SINH'=> 2,
	'SIZE'=> 2,
	'SKIP'=> 2,
	'SNAP'=> 2,
	'SOME'=> 2,
	'SPACEBLOCK'=> 2,
	'SPOOL'=> 2,
	'SQRT'=> 2,
	'STATEMENT'=> 2,
	'STATIC'=> 2,
	'STOP'=> 2,
	'STORAGE'=> 2,
	'STREAM'=> 2,
	'STRING'=> 2,
	'STRINGRANGE'=> 2,
	'STRG'=> 2,
	'STRUCTURE'=> 2,
	'SUBSCRIPTRANGE'=> 2,
	'SUBRG'=> 2,
	'SUBSTR'=> 2,
	'SUBTRACT'=> 2,
	'SUM'=> 2,
	'SUPERCEDE'=> 2,
	'SYSIN'=> 2,
	'SYSPRINT'=> 2,
	'SYSTEM'=> 2,
	'SYSTEM_PROTECTION'=> 2,
	'TAB'=> 2,
	'TAN'=> 2,
	'TAND'=> 2,
	'TANH'=> 2,
	'TEMPORARY'=> 2,
	'%THEN'=> 2,
	'THEN'=> 2,
	'TIME'=> 2,
	'TIMEOUT_PERIOD'=> 2,
	'%TITLE'=> 2,
	'TITLE'=> 2,
	'TO'=> 2,
	'TRANSLATE'=> 2,
	'TRIM'=> 2,
	'TRUNC'=> 2,
	'TRUNCATE'=> 2,
	'UNALIGNED'=> 2,
	'UNAL'=> 2,
	'UNDEFINED'=> 2,
	'UNDF'=> 2,
	'UNDERFLOW'=> 2,
	'UFL'=> 2,
	'UNION'=> 2,
	'UNSPEC'=> 2,
	'UNTIL'=> 2,
	'UPDATE'=> 2,
	'USER_OPEN'=> 2,
	'VALID'=> 2,
	'VALUE'=> 2,
	'VAL'=> 2,
	'VARIABLE'=> 2,
	'VARIANT'=> 2,
	'VARYING'=> 2,
	'VAR'=> 2,
	'VAXCONDITION'=> 2,
	'VERIFY'=> 2,
	'WAIT_FOR_RECORD'=> 2,
	'%WARN'=> 2,
	'WARN'=> 2,
	'WHEN'=> 2,
	'WHILE'=> 2,
	'WORLD_PROTECTION'=> 2,
	'WRITE'=> 2,
	'WRITE_BEHIND'=> 2,
	'WRITE_CHECK'=> 2,
	'X'=> 2,
	'ZERODIVIDE'=> 2,
  );
?>
