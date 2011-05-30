/* This is a ClientTest.cpp modified by Shabaev D.G. (X-Cart Team) */
#include <webpayclient.h>
#include <stdio.h>

void main(int argc, char* argv[]) {
	if( argc < 9 ) {
		printf( "\r\nWebpay Client\r\n" );
		printf( "==================\r\n" );
//		                                  1           2         3         4          5              6              7           8            9         10
		printf( "usage: vault <serverlist> <port> <clientid> <cert path> <cert pass> <total amount> <CC number> <CC exp.date> <Order ref> <Comment>\r\n" );
		exit( 0 );
	}
	
	// initialise the webpay api
	init_client( );
	
	// create a new transaction bundle
	void *bundle = newBundle();
	put ( bundle, "DEBUG", "ON" );

	// Set security related parameters
	put_CertificatePath( bundle, argv[4] );
	put_CertificatePassword( bundle, argv[5] );

	// Set the server address and port number
	setServers ( bundle, argv[1] );
	setPort ( bundle, argv[2] );

	// Set the transaction's parameters.
	// These vary between transaction types and are subject
	// to change with notice, as new types are added.
	put ( bundle, "CLIENTID", argv[3] );
	put ( bundle, "CARDDATA", argv[7] );
	put ( bundle, "CARDEXPIRYDATE", argv[8] );
	put ( bundle, "INTERFACE", "CREDITCARD" );
	put ( bundle, "TRANSACTIONTYPE", "PURCHASE" );
	put ( bundle, "COMMENT", argv[10] );
	put ( bundle, "CLIENTREF", argv[9] );
	put ( bundle, "TERMINALTYPE", "0" );
	put ( bundle, "TOTALAMOUNT", argv[6] );
	put ( bundle, "TAXAMOUNT", "0.00" );

	// Attempt to execute the transaction request...
	if ( execute ( bundle ) ) {
		printf ( "Transaction : [OK]\r\n" );
	} else {
		printf ( "Transaction : [Fail]\r\n" );
	}

	// Get the responses and display them...
	printf ( "Reference : [%s]\r\n", get( bundle, "TXNREFERENCE") );
	printf ( "AuthCode : [%s]\r\n", get( bundle, "AUTHCODE") );
	printf ( "ResponseText : [%s]\r\n", get( bundle, "RESPONSETEXT") );
	printf ( "ResponseCode : [%s]\r\n", get( bundle, "RESPONSECODE") );
	printf ( "ErrorMessage : [%s]\r\n", get( bundle, "ERROR") );
	
	cleanup ( bundle );
	free ( (void *)bundle );
	
	// free the webpay api
	free_client( );
}

