DELETE FROM xcart_taxes;
DELETE FROM xcart_tax_rates;
DELETE FROM xcart_tax_rate_memberships;
DELETE FROM xcart_zones;
DELETE FROM xcart_zone_element;
DELETE FROM xcart_languages_alt WHERE name='tax_1' OR name='tax_2';






INSERT INTO xcart_taxes VALUES (1,'GST','DST+SH','S','Y','N','Y','R','GST 123-456-789-0000',10);
INSERT INTO xcart_taxes VALUES (2,'PST','DST','S','Y','N','Y','R','PST 123-456-789-0000',20);







INSERT INTO xcart_languages_alt VALUES ('en','tax_1','GST/HST');
INSERT INTO xcart_languages_alt VALUES ('en','tax_2','PST');






INSERT INTO xcart_tax_rates VALUES (1,1,2,'',14.000,'%',1);
INSERT INTO xcart_tax_rates VALUES (2,1,1,'',6.000,'%',1);
INSERT INTO xcart_tax_rates VALUES (4,2,9,'DST+GST',10.000,'%',1);
INSERT INTO xcart_tax_rates VALUES (5,2,3,'',0.000,'%',1);
INSERT INTO xcart_tax_rates VALUES (6,2,4,'',5.000,'%',1);
INSERT INTO xcart_tax_rates VALUES (7,2,5,'',7.000,'%',1);
INSERT INTO xcart_tax_rates VALUES (8,2,8,'',8.000,'%',1);
INSERT INTO xcart_tax_rates VALUES (9,2,7,'DST+GST',7.500,'%',1);






INSERT INTO xcart_zones VALUES (1,'Canada (GST=6%)','C1-S10',1);
INSERT INTO xcart_zones VALUES (2,'Canada (GST=14%)','C1-S3',1);
INSERT INTO xcart_zones VALUES (3,'Canada (PST=0%)','C1-S7',1);
INSERT INTO xcart_zones VALUES (4,'Canada (PST=5%)','C1-S1',1);
INSERT INTO xcart_zones VALUES (5,'Canada (PST=7%)','C1-S2',1);
INSERT INTO xcart_zones VALUES (7,'Canada (Quebec, PST=7.5%)','C1-S1',1);
INSERT INTO xcart_zones VALUES (8,'Canada (PST=8%)','C1-S1',1);
INSERT INTO xcart_zones VALUES (9,'Canada (Prince Edward Island, PST=10%)','C1-S1',1);






INSERT INTO xcart_zone_element VALUES (1,'CA','C');
INSERT INTO xcart_zone_element VALUES (1,'CA_AB','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_BC','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_MB','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_NT','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_NU','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_ON','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_PE','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_QC','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_SK','S');
INSERT INTO xcart_zone_element VALUES (1,'CA_YT','S');
INSERT INTO xcart_zone_element VALUES (2,'CA','C');
INSERT INTO xcart_zone_element VALUES (2,'CA_NB','S');
INSERT INTO xcart_zone_element VALUES (2,'CA_NF','S');
INSERT INTO xcart_zone_element VALUES (2,'CA_NS','S');
INSERT INTO xcart_zone_element VALUES (3,'CA','C');
INSERT INTO xcart_zone_element VALUES (3,'CA_AB','S');
INSERT INTO xcart_zone_element VALUES (3,'CA_NB','S');
INSERT INTO xcart_zone_element VALUES (3,'CA_NF','S');
INSERT INTO xcart_zone_element VALUES (3,'CA_NS','S');
INSERT INTO xcart_zone_element VALUES (3,'CA_NT','S');
INSERT INTO xcart_zone_element VALUES (3,'CA_NU','S');
INSERT INTO xcart_zone_element VALUES (3,'CA_YT','S');
INSERT INTO xcart_zone_element VALUES (4,'CA','C');
INSERT INTO xcart_zone_element VALUES (4,'CA_SK','S');
INSERT INTO xcart_zone_element VALUES (5,'CA','C');
INSERT INTO xcart_zone_element VALUES (5,'CA_BC','S');
INSERT INTO xcart_zone_element VALUES (5,'CA_MB','S');
INSERT INTO xcart_zone_element VALUES (7,'CA','C');
INSERT INTO xcart_zone_element VALUES (7,'CA_QC','S');
INSERT INTO xcart_zone_element VALUES (8,'CA','C');
INSERT INTO xcart_zone_element VALUES (8,'CA_ON','S');
INSERT INTO xcart_zone_element VALUES (9,'CA','C');
INSERT INTO xcart_zone_element VALUES (9,'CA_PE','S');


#
# General settings for Canadian demo
#

UPDATE xcart_config SET value='Montreal' WHERE name='location_city';
UPDATE xcart_config SET value='QC' WHERE name='location_state';
UPDATE xcart_config SET value='J1Z6T5' WHERE name='location_zipcode';
UPDATE xcart_config SET value='CA' WHERE name='location_country';

UPDATE xcart_config SET value='CA' WHERE name='default_country';
UPDATE xcart_config SET value='QC' WHERE name='default_state';
UPDATE xcart_config SET value='Montreal' WHERE name='default_city';
UPDATE xcart_config SET value='J1Z6T5' WHERE name='default_zipcode';
UPDATE xcart_config SET value='Y' WHERE name='apply_default_country';

UPDATE xcart_config SET value='CA$' WHERE name='currency_symbol';
UPDATE xcart_config SET value='US$' WHERE name='alter_currency_symbol';
UPDATE xcart_config SET value='0.736153' WHERE name='alter_currency_rate';

UPDATE xcart_config SET value='%d/%m/%Y' WHERE name='date_format';

UPDATE xcart_shipping SET active='N' WHERE code!='CPC';

UPDATE xcart_languages SET value='Province/Territory' WHERE name='lbl_state' AND code='en';
UPDATE xcart_languages SET value='Postal code' WHERE name='lbl_zip_code' AND code='en';
UPDATE xcart_languages SET value='CANADA TOLL FREE' WHERE name='lbl_phone_1_title' AND code='en';

