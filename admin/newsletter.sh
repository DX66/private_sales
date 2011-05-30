#!/bin/sh

#
# $Id: newsletter.sh,v 1.2 2006/03/17 11:52:49 mclap Exp $
#
# Newsletter mailling script
#

#
# To make this script use sendmail instead of mail
# comment out the line with mail_prog definition
#
#mail_prog="mail"
sendmail_prog="sendmail"

if [ "x$REPLYTO" != "x" ]; then
	sendmail_prog="$sendmail_prog -bm -t -i -f $REPLYTO"
fi

#
# Get mail list
#
if [ "${1}" != "" ] 
then 
	mail_list=`cat "${1}"`
fi

#
# Get mail subject
#
if [ "${2}" != "" ] 
then 
	mail_subj=`cat "${2}"`
fi

#
# Get mail body
#
mail_body="${3}"

#
# Get mail "From"
#
if [ "${4}" != "" ] 
then 
	mail_from="${4}"
fi

#
# Get charset
#
if [ "${5}" != "" ] 
then 
	mail_addheader="${5}"
fi

#
# Send mail to all in maillist
#
for target in $mail_list
	do
	if [ x$mail_prog != x ]; then
		(sed "s/###EMAIL###/$target/g" < $mail_body) | $mail_prog -s "$mail_subj" "$target"
	else
		(echo -e "To: $target\nFrom: $mail_from\nSubject: $mail_subj\n$mail_addheader"; sed "s/###EMAIL###/$target/g" < $mail_body) | $sendmail_prog $target
	fi
done

#
# Delete files
#
rm "$1" "$2" "$3"
