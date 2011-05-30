DELETE FROM xcart_taxes;
DELETE FROM xcart_tax_rates;
DELETE FROM xcart_zones;
DELETE FROM xcart_zone_element;
DELETE FROM xcart_languages_alt WHERE name='tax_1' OR name='tax_2';





INSERT INTO xcart_taxes VALUES (1,'GST','DST','S','Y','N','N','','ABN: 123-4567-7890',0);





REPLACE INTO xcart_languages_alt VALUES ('en','tax_1','GST');





INSERT INTO xcart_tax_rates VALUES (1,1,1,'',10.000,'%',1);





INSERT INTO xcart_zones VALUES (1,'Australia','C1-S8',1);
INSERT INTO xcart_zones VALUES (2,'New Zealand','C1',1);





INSERT INTO xcart_zone_element VALUES (1,'AU','C');
INSERT INTO xcart_zone_element VALUES (1,'AU_ACT','S');
INSERT INTO xcart_zone_element VALUES (1,'AU_NSW','S');
INSERT INTO xcart_zone_element VALUES (1,'AU_NT','S');
INSERT INTO xcart_zone_element VALUES (1,'AU_QLD','S');
INSERT INTO xcart_zone_element VALUES (1,'AU_SA','S');
INSERT INTO xcart_zone_element VALUES (1,'AU_TAS','S');
INSERT INTO xcart_zone_element VALUES (1,'AU_VIC','S');
INSERT INTO xcart_zone_element VALUES (1,'AU_WA','S');
INSERT INTO xcart_zone_element VALUES (2,'NZ','C');





DELETE FROM xcart_config WHERE name='use_counties';

UPDATE xcart_config SET value='+61-800-555-5555' WHERE name='company_phone';
UPDATE xcart_config SET value='+61-800-555-5555' WHERE name='company_fax';
UPDATE xcart_languages SET value='AUSTRALIA TOLL FREE' WHERE name='lbl_phone_1_title' AND code='en';

UPDATE xcart_config SET value='%d/%m/%Y' WHERE name='date_format';

UPDATE xcart_config SET value='189 Toorak Road' WHERE name='location_address';
UPDATE xcart_config SET value='Melbourne' WHERE name='location_city';
UPDATE xcart_config SET value='3002' WHERE name='location_zipcode';
UPDATE xcart_config SET value='VIC' WHERE name='location_state';
UPDATE xcart_config SET value='AU' WHERE name='location_country';

UPDATE xcart_config SET value='Melbourne' WHERE name='default_city';
UPDATE xcart_config SET value='3004' WHERE name='default_zipcode';
UPDATE xcart_config SET value='VIC' WHERE name='default_state';
UPDATE xcart_config SET value='AU' WHERE name='default_country';
UPDATE xcart_config SET value='Y' WHERE name='apply_default_country';

UPDATE xcart_config SET value='AUD ' WHERE name='currency_symbol';
UPDATE xcart_config SET value='' WHERE name='alter_currency_symbol';
UPDATE xcart_config SET value='' WHERE name='alter_currency_rate';

UPDATE xcart_config SET value='N' WHERE name='display_taxed_order_totals';
UPDATE xcart_config SET value='Y' WHERE name='display_cart_products_tax_rates';

UPDATE xcart_config SET value='1000' WHERE name='weight_symbol_grams';
UPDATE xcart_config SET value='kg' WHERE name='weight_symbol';


REPLACE INTO xcart_languages VALUES ('en','lbl_zipcode_masks','Postal code masks','Labels');
REPLACE INTO xcart_languages VALUES ('en','lbl_zipcode_mask_examples','Postal code mask examples','Text');
REPLACE INTO xcart_languages VALUES ('en','lbl_zip_code','Postal code','Labels');
REPLACE INTO xcart_languages VALUES ('en','lbl_zip_postal_codes','Postal codes','Labels');
REPLACE INTO xcart_languages VALUES ('en','lbl_origin_zip_code','Origin postal code','Labels');
REPLACE INTO xcart_languages VALUES ('en','opt_default_zipcode','Default postal code','Options');
REPLACE INTO xcart_languages VALUES ('en','opt_location_zipcode','Company postal code','Options');

UPDATE xcart_languages SET value='Federal State' WHERE name='lbl_state' AND code='en';
UPDATE xcart_languages SET value='Federal States Management' WHERE name='lbl_states_management' AND code='en';
UPDATE xcart_languages SET value='Federal States' WHERE name='lbl_states' AND code='en';

