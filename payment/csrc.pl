#!/usr/bin/perl 

# Copyright 1996, all rights reserved, CyberSource Corporation.
# modified by Dmitriy Shabaev, Maxim Shamaev (c) X-Cart team

if ($#ARGV == -1) {
	exit;
}

while(<STDIN>) {
	($a,$b) = split(/=/,$_,2); chomp($b);
	if($a eq "ics_path") {
		$path = $b;
	} else {
		 $ics_req{$a} = $b;
	}
}


push (@INC, $path."/lib");
push (@INC, $path."/i386-linux");
push (@INC, $path."/i386-linux-thread-multi");
require 'ics2-lib.pl';
$ENV{ICSPATH} = $path;

# send request
%ics_res = &ics_send(%ics_req);
&ics_print(%ics_res);
