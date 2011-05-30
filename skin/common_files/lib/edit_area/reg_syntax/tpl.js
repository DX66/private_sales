/*
* last update: 2006-08-24
*/

editAreaLoader.load_syntax["tpl"] = {
	'COMMENT_SINGLE' : {}
	,'COMMENT_MULTI' : {'{*' : '*}'}
	,'QUOTEMARKS' : {1: "'", 2: '"'}
	,'KEYWORD_CASE_SENSITIVE' : true
	,'KEYWORDS' : {
	}
	,'OPERATORS' :[
	]
	,'DELIMITERS' :[
	]
	,'REGEXPS' : {
		'tags' : {
			'search' : '(\{)(/?[a-z][^ \r\n\t}]*)([^}]*\})'
			,'class' : 'tags'
			,'modifiers' : 'g'
			,'execute' : 'before' // before or after
		}
		,'htmltags' : {
			'search' : '(<)(/?[a-z][^ \r\n\t>]*)([^>]*>)'
			,'class' : 'htmltags'
			,'modifiers' : 'gi'
			,'execute' : 'before' // before or after
		}
		,'attributes' : {
			'search' : '( |\n|\r|\t)([^ \r\n\t=]+)(=)'
			,'class' : 'attributes'
			,'modifiers' : 'g'
			,'execute' : 'before' // before or after
		}
	}
	,'STYLES' : {
		'COMMENTS': 'color: #AAAAAA;'
		,'QUOTESMARKS': 'color: #6381F8;'
		,'KEYWORDS' : {
			}
		,'OPERATORS' : 'color: #E775F0;'
		,'DELIMITERS' : ''
		,'REGEXPS' : {
			'attributes': 'color: #B1AC41;'
			,'tags': 'color: #E62253;'
			,'htmltags': 'color: green;'
			,'test': 'color: #00FF00;'
		}	
	}		
};
